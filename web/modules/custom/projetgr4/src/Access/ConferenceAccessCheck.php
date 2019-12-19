<?php
namespace Drupal\projetgr4\Access;

use Drupal\node\NodeInterface;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\Access\AccessInterface;

class ConferenceAccessCheck implements AccessInterface{
  public function access(RouteMatch $route, NodeInterface $node,  AccountInterface $account){
    //ksm($node);

    return AccessResult::allowedIf( $node->bundle() === 'programmes');
      }
  }

