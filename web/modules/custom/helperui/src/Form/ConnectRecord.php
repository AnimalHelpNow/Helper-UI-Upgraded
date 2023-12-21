<?php

namespace Drupal\helperui\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Connect Record form.
 */
class ConnectRecord extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'connectrecord_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['uniqueid'] = [
      '#type' => 'textfield',
      '#title' => t('Enter the UniqueID that was included in the account invitation email:'),
    ];

    $form['secret_code'] = [
      '#type' => 'textfield',
      '#title' => t('Enter the Secret Code that was included in the account invitation email:'),
    ];

    // Adds a simple submit button that refreshes the form and
    // clears its contents this is the default behavior for forms.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Continue',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $uniqueid = $form_state->getValues()['uniqueid'];
    $secret_code = $form_state->getValues()['secret_code'];
    $uid = \Drupal::currentUser()->id();

    $query = \Drupal::database()->select('node__field_secret_code', 'n');
    $query->fields('n', ['entity_id', 'field_secret_code_value']);
    $query->condition('n.entity_id', $uniqueid);
    $query->condition('n.field_secret_code_value', $secret_code);
    $results = $query->execute()->fetchAll();

    $nid = (count($results) > 0) ? $results[0]->entity_id : 0;
    if ($nid > 0) {
      // Change the Helper record author to this user.
      $node = Node::load($nid);
      if ($node) {
        $node->set('uid', $uid);
        $node->save();

        // Redirect to the user record.
        $url = Url::fromUserInput('/node/' . $nid);
        $form_state->setRedirectUrl($url);
      }
    }
    else {
      \Drupal::messenger()->addMessage(t('A published record was not found for that UniqueID and Secret Code!  Please enter the UniqueID and Secret Code which were sent to you by AnimalHelpNow!'));
    }
  }

}
