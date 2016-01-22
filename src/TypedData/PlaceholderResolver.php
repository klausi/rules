<?php

/**
 * @file
 * Contains \Drupal\rules\TypedData\PlaceholderResolver.
 */

namespace Drupal\rules\TypedData;

use Drupal\Component\Render\HtmlEscapedText;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\TypedData\Exception\MissingDataException;

/**
 * Resolver for placeholder tokens based upon typed data.
 */
class PlaceholderResolver implements PlaceholderResolverInterface {

  /**
   * The typed data manager.
   *
   * @var \Drupal\rules\TypedData\TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * The data filter manager.
   *
   * @var \Drupal\rules\TypedData\DataFilterManagerInterface
   */
  protected $dataFilterManager;

  /**
   * Constructs the object.
   *
   * @param \Drupal\rules\TypedData\TypedDataManagerInterface $typed_data_manager
   *   The typed data manager.
   * @param \Drupal\rules\TypedData\DataFilterManagerInterface $data_filter_manager
   *   The data filter manager.
   */
  public function __construct(TypedDataManagerInterface $typed_data_manager, DataFilterManagerInterface $data_filter_manager) {
    $this->typedDataManager = $typed_data_manager;
    $this->dataFilterManager = $data_filter_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function resolvePlaceholders($text, array $data = [], BubbleableMetadata $bubbleable_metadata = NULL, array $options = []) {
    $options += [
      'langcode' => NULL,
      'clear' => FALSE,
    ];
    $placeholder_by_data = $this->scan($text);
    if (empty($placeholder_by_data)) {
      return $text;
    }

    $replacements = [];
    $data_fetcher = $this->typedDataManager->getDataFetcher();
    foreach ($placeholder_by_data as $data_name => $placeholders) {
      foreach ($placeholders as $placeholder_main_part => $placeholder) {
        try {
          if (!isset($data[$data_name])) {
            throw new MissingDataException("There is no data with the name '$data_name' available.");
          }
          list ($property_sub_paths, $filters) = $this->parseMainPlaceholderPart($placeholder_main_part, $placeholder);
          $fetched_data = $data_fetcher->fetchBySubPaths($data[$data_name], $property_sub_paths, $bubbleable_metadata, $options['langcode']);

          // Apply filters.
          if ($filters) {
            $value = $fetched_data->getValue();
            $definition = $fetched_data->getDataDefinition();
            foreach ($filters as $data) {
              list($filter_id, $arguments) = $data;
              $filter = $this->dataFilterManager->createInstance($filter_id);
              if (!$filter->allowsNullValues() && !isset($value)) {
                throw new MissingDataException("There is no data value for filter '$filter_id' to work on.");
              }
              $value = $filter->filter($definition, $value, $arguments, $bubbleable_metadata);
              $definition = $filter->filtersTo($definition, $arguments);
            }
          }
          else {
            $value = $fetched_data->getString();
          }

          // Escape the tokens, unless they are explicitly markup.
          $replacements[$placeholder] = $value instanceof MarkupInterface ? $value : new HtmlEscapedText($value);
        }
        catch (\InvalidArgumentException $e) {
          // Should we log warnings if there are problems other than missing
          // data, like syntactically invalid placeholders?
          if (!empty($options['clear'])) {
            $replacements[$placeholder] = '';
          }
        }
        catch (MissingDataException $e) {
          if (!empty($options['clear'])) {
            $replacements[$placeholder] = '';
          }
        }
      }
    }
    return $replacements;
  }

  /**
   * Parses the main placeholder part.
   *
   * Main placeholder parts look like 'property:property|filter(arg)|filter'.
   *
   * @param string $main_part
   *   The main placeholder part.
   * @param string $placeholder
   *   The full placeholer string.
   *
   * @return array[]
   *   An numerically indexed arrays containing:
   *   - The numerically indexed array of property sub-paths.
   *   - The numerically indexed array of parsed filter expressions, where each
   *     entry is another numerically indexed array containing two items: the
   *     the filter id and the array of filter arguments.
   *
   * @throws \InvalidArgumentException
   *   Thrown if in invalid placeholders are to be parsed.
   */
  protected function parseMainPlaceholderPart($main_part, $placeholder) {
    $properties = explode(':', $main_part);
    $last_part = array_pop($properties);
    $filter_expressions = explode('|', $last_part);
    // The first part, before the first |, is actually the last property.
    // Also be sure to remove potential whitespace after the last property.
    $properties[] = rtrim(array_shift($filter_expressions));
    $filters = [];

    foreach ($filter_expressions as $expression) {
      // Look for filter arguments.
      $matches = [];
      preg_match_all('/
      ([^\(]+)
      \(             # ( - pattern start
       (.+)
      \)             # ) - pattern end
      /x', $expression, $matches);

      $filter_id = isset($matches[1][0]) ? $matches[1][0] : $expression;
      // Be sure to remove all whitespaces.
      $filter_id = str_replace(' ', '', $filter_id);
      $args = array_map(function ($arg) {
        // Remove surrounding whitespaces and then quotes.
        return trim(trim($arg), "'");
      }, explode(',', isset($matches[2][0]) ? $matches[2][0] : ''));

      $filters[] = [$filter_id, $args];
    }
    return [$properties, $filters];
  }

  /**
   * {@inheritdoc}
   */
  public function replacePlaceHolders($text, array $data = [], BubbleableMetadata $bubbleable_metadata = NULL, array $options = []) {
    $replacements = $this->resolvePlaceholders($text, $data, $bubbleable_metadata, $options);

    $placeholders = array_keys($replacements);
    $values = array_values($replacements);

    return str_replace($placeholders, $values, $text);
  }

  /**
   * {@inheritdoc}
   */
  public function scan($text) {
    // Matches tokens with the following pattern: [$name:$property_path]
    // $name and $property_path may not contain [ ] characters.
    // $name may not contain : or whitespace characters, but $property_path may.
    preg_match_all('/
      \[             # [ - pattern start
      ([^\[\]:]+)  # match $type not containing whitespace : [ or ]
      :              # : - separator
      ([^\[\]]+)     # match $name not containing [ or ]
      \]             # ] - pattern end
      /x', $text, $matches);

    $names = $matches[1];
    $tokens = $matches[2];

    // Iterate through the matches, building an associative array containing
    // $tokens grouped by $types, pointing to the version of the token found in
    // the source text. For example,
    // $results['node']['title'] = '[node:title]';.
    $results = [];
    for ($i = 0; $i < count($tokens); $i++) {
      $results[ltrim($names[$i])][rtrim($tokens[$i])] = $matches[0][$i];
    }

    return $results;
  }

}
