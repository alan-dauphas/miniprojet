<?php

namespace Drupal\projetgr4\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;
use Drupal\Core\Access\AccessResult;

class ProgrammesController extends ControllerBase{

     public function programme_status(UserInterface $user)
    {

      $storage = \Drupal::entityTypeManager()->getStorage('webform_submission');
      $webform_submission = $storage->loadByProperties([
        'entity_type' => 'node',
        'uid' => $user->id(),
      ]);

      $submission_data = array();
      foreach ($webform_submission as $submission) {
        $submission_data[] = [$submission->getSourceEntity()->getTitle(), $submission->getData()['status'], ];
      }
      $status_table = [
        '#type' => 'table',
        '#header' => ['Titre de Programme', 'Status'],
        '#rows' => $submission_data,
        '#empty'=> $this->t('You are not register any conference yet'),
      ];
      return [$status_table];
    }

    public function profile()
    {
      $user = \Drupal::routeMatch()->getParameter('user');
      $account = \Drupal\user\Entity\User::load($user);
      $date_formatter = \Drupal::service('date.formatter');
      $user_profile = [];
      $user_profile[] = [
                          $account->get('field_nom')->value,
                          $account->get('field_prenom')->value,
                          $account->get('mail')->value,
                          $account->get('name')->value,
                          $account->get('field_organisation_societe')->value,
                          $account->get('field_site_personnel')->value,
                          $account->getRoles()[1],
                          $date_formatter->format($account->get('login')->value, 'custom', 'H:i'),
                          ];

      $user_table = [
        '#type' => 'table',
        '#header' => ['Nom', 'Prenom', 'Email', 'Log In Name', 'Organisation', 'Site Personnel', 'Designation',
                      'Last Login'],
        '#rows' => $user_profile,
        '#empty'=> $this->t('Sorry User Info Not Available'),
      ];

      return [$user_table];
    }

    public function access(){
      $logged_in_anonymous = \Drupal::currentUser()->isAnonymous();
      if($logged_in_anonymous){
        return AccessResult::forbidden();
      }
      return AccessResult::allowed();
    }
  public function email(){
    $is_organisateur = \Drupal::currentUser()->getRoles();
    if($is_organisateur == 'organisateur' || $is_organisateur == 'administrator' ){
      return AccessResult::allowed()();
    }
    return AccessResult::forbidden();
  }


//  public function list_participants(Drupal\node\NodeInterface $node){
//
//      $nbNode = $node->id(); // Retourne le numéro (id) du node
//    $query = \Drupal::database()->select('webform_submission_data', 's');
//    $query->fields('s', ['sid'])
//      ->condition('s.name', 'status')
//      ->condition('s.value', 'acceptée');
//    $sid_entity_id = $query->execute();
//
//    $participants = [];
//    foreach ($sid_entity_id as $sid){
//
////    $list = $this->entityTypeManager()->getStorage('webform_submission')
////        ->loadByProperties(['sid'=> $sid]);
//      $query = \Drupal::database()->select('webform_submission', 's');
//      $query->fields('s', ['uid'])
//        ->condition('s.sid', $sid);
//
//      $user_id = $query->execute();
//      kint($user_id);
//
//
//
//      foreach($user_id as $owner){
//
////        $owners = $owner->getOwnerId(); // Retourne le numéro (id) d'un participants
//        $account = Drupal::entityTypeManager()->getStorage('user')->load($owner);
//        $participants[] = [$account->get('field_prenom')->value, $account->get('field_nom')->value,
//               $account->get('mail')->value,];
//
//      }
//
//      $list_participant =  [
//            '#type' => 'table',
//            '#header' => ['Nom', 'Prenom', 'Email'],
//            '#rows' => $participants,
//            '#empty'=> $this->t('none participation yet'),
//          ];
//
//      }
//    return [$list_participant];
//  }
  public function list_participants(Drupal\node\NodeInterface $node){
    $nbNode = $node->id(); // Retourne le numéro (id) du node
    $list = $this->entityTypeManager()->getStorage('webform_submission')
      ->loadByProperties(['entity_id'=> $nbNode]);
    $participants = [];
    foreach($list as $owner){
      $owner->getData()['status'];
      $owners = $owner->getOwnerId(); // Retourne le numéro (id) d'un participants
      $account = Drupal::entityTypeManager()->getStorage('user')->load($owners);
      $participants[] = [$account->get('field_prenom')->value, $account->get('field_nom')->value,
        $account->get('mail')->value,];
    }
    $list_participant =  [
      '#type' => 'table',
      '#header' => ['Nom', 'Prenom', 'Email'],
      '#rows' => $participants,
      '#empty'=> $this->t('none participation yet'),
    ];
    return [$list_participant];
  }
}
