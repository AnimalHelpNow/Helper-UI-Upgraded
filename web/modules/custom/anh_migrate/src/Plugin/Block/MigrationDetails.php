<?php

namespace Drupal\anh_migrate\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FormatterPluginManager;
use Psr\Log\LoggerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;


/**
 * Provides a block for fox services and contact us footer.
 *
 * @Block(
 *   id = "migration_details",
 *   admin_label = @Translation("Node Migration Details"),
 *   category = "fox"
 * )
 */
class MigrationDetails extends BlockBase {

    use StringTranslationTrait;

    // Content Type > migration name(s) ...
    protected $node_migration_ids = [
        'animalgroup' => [
            'node_animalgroup',
        ],
        'contact' => [
            'node_contact'
        ],
        'country' => [
            'node_country'
        ],
        'faqs' => [
            'node_faqs'
        ],
        'helper_jurisdiction' => [
            'node_helper_jurisdiction'
        ],
        'helper' => [
            'd7_helper_node'
        ],
        'helpermodify' => [
            'node_helpermodify'
        ],
        'helpertype' => [
            'node_helpertype'
        ],
        'holiday' => [
            'node_holiday'
        ],
        'jurisdiction_area' => [
            'node_jurisdiction_area'
        ],
        'jurisdiction_type' => [
            'node_jurisdiction_type'
        ],
        'local_jurisdiction' => [
            'node_local'
        ],
        'location_level_1' => [
            'node_location_level_1'
        ],
        'location_level_2' => [
            'node_location_level_2'
        ],
        'page' => [
            'node_page'
        ],
        'press_release' => [
            'node_press_release'
        ],
        'profile' => [
            'node_profile'
        ],
        'resource' => [
            'node_resource'
        ],
        'teamcommunication' => [
            'node_teamcommunication'
        ]
    ];

    protected $prefix = "migrate_map_";

    public function getCacheMaxAge() {
        return 0;
    }

  /**
   * {@inheritdoc}
   */
    public function build() {
       $output = [
        'message' => [
            '#type' => 'markup',
            '#markup' => 'No Results'
        ]
       ];
       
       // Check if this is a node we are looking at.
       $node = \Drupal::routeMatch()->getParameter('node');
       if ($node instanceof \Drupal\node\NodeInterface && isset($node)) {
        $this->buildNodeOutput($node, $output);
        return $output;
       }

       // Check if this is a term we are looking at.
       $term = \Drupal::routeMatch()->getParameter('taxonomy_term');
       if ($term instanceof \Drupal\taxonomy\Entity\Term && isset($term)) {
        $this->buildTermOutput($term, $output);
       }

       // Check if this is a term we are looking at.
       $group = \Drupal::routeMatch()->getParameter('group');
       if ($group instanceof \Drupal\group\Entity\GroupInterface && isset($group) && $group->bundle() == 'morg') {
        $this->buildMorgOutput($group, $output);
       }

       return $output;
    }

    protected function buildMorgOutput($group, &$output) {
        $results = $this->getDetails("morgs", $group->id());
        
        
        if ($results) {
            $output['message'] = [
                '#type' => 'markup',
                '#markup' => 
                    "<ul>
                        <li>Drupal 7 Location: 
                            <a 
                                target='_blank' 
                                href='http://dev-animal-help-network.pantheonsite.io/admin/content/fox_morgs/fox_morg/{$results->sourceid1}/edit'>
                                http://dev-animal-help-network.pantheonsite.io/admin/content/fox_morgs/fox_morg/{$results->sourceid1}/edit
                            </a>
                        </li>
                    </ul>"
            ];
        }

    }

    protected function buildTermOutput($term, &$output) {
        $results = $this->getDetails("taxonomy_terms", $term->id());
        
        
        if ($results) {
            $output['message'] = [
                '#type' => 'markup',
                '#markup' => 
                    "<ul>
                        <li>Drupal 7 Location: 
                            <a 
                                target='_blank' 
                                href='http://dev-animal-help-network.pantheonsite.io/taxonomy/term/{$results->sourceid1}/edit'>
                                http://dev-animal-help-network.pantheonsite.io/taxonomy/term/{$results->sourceid1}
                            </a>
                        </li>
                    </ul>"
            ];
        }

    }

    protected function buildNodeOutput($node, &$output) {
        $results = FALSE;
        if (isset($this->node_migration_ids[$node->bundle()])) {
            if ( is_array($this->node_migration_ids[$node->bundle()])) {
                foreach($this->node_migration_ids[$node->bundle()] as $migration_id) {
                    $results = $this->getDetails($migration_id, $node->id());
                    if (isset($results->destid1)) {
                        break;
                    }
                }
            } else if (isset($this->node_migration_ids[$node->bundle()])) {
                $results = $this->getDetails($this->node_migration_ids[$node->bundle()], $node->id());
            }
        }
        if ($results) {
            $output['message'] = [
                '#type' => 'markup',
                '#markup' => 
                    "<ul>
                        <li>Drupal 7 Location: 
                            <a 
                                target='_blank' 
                                href='http://dev-animal-help-network.pantheonsite.io/node/{$results->sourceid1}/edit'>
                                http://dev-animal-help-network.pantheonsite.io/node/{$results->sourceid1}
                            </a>
                        </li>
                    </ul>"
            ];
        }
    }

    /**
     * 
     */
    protected function getDetails($migrate_id, $id) {
        
        $conn = \Drupal::database();
        $query = $conn->select($this->prefix . $migrate_id, 'a');
        $query->condition('destid1', $id);
        $query->fields('a');
        $results = $query->execute()
            ->fetchAllAssoc('destid1');
        return isset($results[$id]) ? $results[$id] : FALSE;
    }

}
