<?php

namespace Drupal\helperui\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Am I Listed form.
 */
class AmIListedForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'amilisted_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // This is the first form element. It's a textfield with a label, "Name".
    $form['#attached']['library'][] = 'helperui/global';
    $form['phone'] = [
      '#type' => 'textfield',
      '#title' => t('Enter a phone number affiliated with your facility:'),
      '#attributes' => ['class' => ['masked']],
    ];

    // Adds a simple submit button that refreshes the form and
    // clears its contents -- this is the default behavior for forms.
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
    $phone = $form_state->getValue('phone');
    $form_state->setRedirect('view.helpers_phone_search.page_1', ['arg_0' => $phone]);
  }

}
