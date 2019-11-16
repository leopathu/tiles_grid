<?php

namespace Drupal\tiles_grid\Controller;

use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Tiles Grid routes.
 */
class TilesGridController extends ControllerBase {

  /*
   * Tiles lists page response.
   */
  public function list() {

    // Get content list and order from tiles entity queue.
    $subqueue = $this->entityTypeManager()->getStorage('entity_subqueue')->load('tiles');
    $subqueue_items = $subqueue->get('items')->getValue();

    $nids = [];
    foreach ($subqueue_items as $subqueue_item) {
      $nids[] = $subqueue_item['target_id'];
    }
    $nodes = Node::loadMultiple($nids);

    $filter_tags = [];

    foreach ($nodes as &$node) {

      // Get formatted content value from configured display mode.
      $rendered_field = $node->field_tile_image->view('tiles');
      $node->image = drupal_render($rendered_field);
      $rendered_field = $node->field_tile_video->view('tiles');
      $node->video = drupal_render($rendered_field);

      $rendered_field = $node->body->view('tiles');
      $node->desc = drupal_render($rendered_field);

      $articles = $node->get('field_article')->getValue();
      $filter_class = '';
      $tids = [];
      // Load article content's Tags if it is available
      if (!empty($articles)) {
        $article = Node::load($articles[0]['target_id']);
        $vals = $article->get('field_tags')->getValue();
        foreach ($vals as $val) {
          $tids[] = $val['target_id'];
          $filter_class .= "filter-{$val['target_id']} ";
        }
      }
      // Load Tiles Tags values if tags not mentioned in the article.
      if (empty($tids)) {
        $vals = $node->get('field_tile_tags')->getValue();
        foreach ($vals as $val) {
          $tids[] = $val['target_id'];
          $filter_class .= "filter-{$val['target_id']} ";
        }
      }

      $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadMultiple($tids);
      $tag_icons = '';
      //print_r($terms);exit;
      foreach ($terms as $term) {
        $term_view = $term->field_tile_icons->view('full');
        $tag_icons.= drupal_render($term_view);
        $filter_tags[$term->id()] = $term;
      }

      $node->filter_class = $filter_class; // Tags list for Isotope filters.
      $node->tags = $tag_icons;
    }


    return [
      '#theme' => 'tiles_grid_list',
      '#tiles' => $nodes,
      '#tags' => $filter_tags,
    ];

  }

}
