<?php

namespace Drupal\projetgr4\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;


class ProgrammesController extends ControllerBase{

    public function programme_status()
    {
      $user = Drupal::routeMatch()->getParameter('user');
      $query = Drupal::database()->select('webform_submission', 's');
      $query->fields('s', ['sid', 'entity_id'])
        ->condition('s.uid', $user);
      $sid_entity_id = $query->execute();
      if(!empty($sid_entity_id)){
        $user_programme_status = [];
        foreach($sid_entity_id as $info_ids){

        $query = Drupal::database()->select('webform_submission', 'w');
        $query->join('webform_submission_data', 'd', "d.sid = $info_ids->sid AND d.name = 'status'");
        $query->join('node_field_data', 'n', "n.nid = $info_ids->entity_id");
        $result = $query
          ->fields('d', array('value'))
          ->fields('n', array('title'))
          ->execute();
          foreach ($result as $record){
            $user_programme_status[]= [$record->title, $record->value,];
          }
        }
        $status_table = [
          '#type' => 'table',
          '#header' => ['Programme Title', 'Status'],
          '#rows' => $user_programme_status,
          '#empty'=> $this->t('You are not register any conference yet'),
        ];

        return [$status_table];

      }
        $no_data_return_table = [
        '#type' => 'table',
        '#header' => ['Programme Title', 'Status'],
        '#rows' => [
          'data' => [
            [
              'colspan' => 2,
              'data' => 'You are not register any Programme Yet',
              'style' => 'background-color: pink; text-align: center'
            ]
          ],
        ],
      ];
      return [$no_data_return_table];
    }

  public function profile()
  {
    $user = Drupal::routeMatch()->getParameter('user');
    $account = Drupal\user\Entity\User::load($user);
    $date_formatter = Drupal::service('date.formatter');
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

    $status_table = [
      '#type' => 'table',
      '#header' => ['Nom', 'Prenom', 'Email', 'Log In Name', 'Organisation', 'Site Personnel', 'Designation', 'Last Login'],
      '#rows' => $user_profile,
      '#empty'=> $this->t('You are not register any conference yet'),
    ];

    return [$status_table];
  }


  public function list_participants(Drupal\node\NodeInterface $node){

      $nbNode = $node->id(); // Retourne le numéro (id) du node

      $list = $this->entityTypeManager()->getStorage('webform_submission')
        ->loadByProperties(['entity_id'=> $nbNode]);

      $participants = [];

      foreach($list as $owner){
        $owners = $owner->getOwnerId(); // Retourne le numéro (id) d'un participants
        $account = Drupal::entityTypeManager()->getStorage('user')->load($owners);

        $prenom = $account->get('field_prenom')->value;
        $nom = $account->get('field_nom')->value;

        $participant = $prenom . ' ' . ' ' . $nom;

        $participants[] = $participant;
      }


        return [
          '#theme' => 'item_list',
          '#items' => $participants,
          '#title' => $this->t('Liste des participants'),
        ];
      }

}
