<?php

namespace Drupal\projetgr4\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Console\Bootstrap\Drupal;
use Drupal\user\UserInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;


class ProgrammesController extends ControllerBase{

    public function programme_status(\Drupal\user\UserInterface $user)
    {
      $query = \Drupal::database()->select('webform_submission', 's');
      $query->fields('s', ['sid', 'entity_id'])
        ->condition('s.uid', $user->id());
      $sid_entity_id = $query->execute();

      if(!empty($sid_entity_id)){
        $user_programme_status = [];
        foreach($sid_entity_id as $info_ids){

        $query = \Drupal::database()->select('webform_submission', 'w');
        $query->join('webform_submission_data', 'd', "d.sid = $info_ids->sid AND d.name = 'status'");
        $query->join('node_field_data', 'n', "n.nid = $info_ids->entity_id");
        $result = $query
          ->fields('d', array('value'))
          ->fields('n', array('title'))
          ->execute();
          foreach ($result as $record){
            $user_programme_status[]= [$record->title, $record->value,];
          }
          $status_table = [
            '#type' => 'table',
            '#header' => ['Programme Title', 'Status'],
            '#rows' => $user_programme_status,
            '#empty'=> $this->t('You are not register any conference yet'),
          ];
          return [$status_table];
        }
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
}
