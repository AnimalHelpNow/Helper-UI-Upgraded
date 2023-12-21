<?php

namespace Drupal\helperui\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Get Listed Form.
 */
class GetListedForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'getlisted_form';
  }

  /**
   * First step of Get listed form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Redirect user if not logged in.
    if (\Drupal::currentUser()->isAnonymous()) {
      return new RedirectResponse('/get-listed-process');
    }

    if ($form_state->has('page') && $form_state->get('page') == 2) {
      return self::formPageTwo($form, $form_state);
    }

    $form_state->set('page', 1);

    $form['getlisted'] = [
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['getlisted']['helpertype'] = [
      '#type' => 'select',
      '#title' => t('Select one of the following Helper Types:'),
      '#options' => [
        0 => t('Select'),
        1 => t('Home-Based Wildlife Rehabilitator'),
        2 => t('Home-Based Wildlife Rehabilitation Network'),
        3 => t('Wildlife Rehabilitation Center'),
        4 => t('Wildlife Rescue'),
        5 => t('Wildlife Hotline'),
        6 => t('Veterinarian Who Treats Wildlife'),
        7 => t('Humane Wildlife Control Operator'),
        8 => t('Humane Wildlife Control Consultant'),
        9 => t('Wildlife Transporter'),
      ],
      '#default_value' => (isset($form_state->getValues()['helpertype'])) ? $form_state->getValues()['helpertype'] : 0,
    ];

    $form['clear'] = [
      '#type' => 'submit',
      '#value' => 'Back',
      '#validate' => ['::clearForm'],
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#value' => 'Continue',
      '#submit' => ['::submitPageOne'],
    ];

    return $form;
  }

  /**
   * Submit first step on form.
   */
  public function submitPageOne(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('page_values', [
        'helpertype' => $form_state->getValue('helpertype'),
      ])
      ->set('page', 2)
      ->setRebuild(TRUE);
  }

  /**
   * Clear form fields and reset.
   */
  public function clearForm(array &$form, FormStateInterface $form_state) {
    $form_state
      ->setUserInput([])
      ->set('page', 1)
      ->setRebuild(TRUE);
  }

  /**
   * Second step of form.
   */
  public function formPageTwo(array &$form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'helperui/global';
    $form['primaryphone'] = [
      '#type' => 'textfield',
      '#title' => t('What is your primary phone number?'),
      '#attributes' => ['class' => ['masked']],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];

    return $form;
  }

  /**
   * Second step form submission.
   */
  public function pageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      ->setValues($form_state->get('page_values'))
      ->set('page', 1)
      ->setRebuild(TRUE);
  }

  /**
   * Form validation.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    switch ($form_state->get('page')) {
      case 1:
        if (empty($form_state->getValues()['helpertype']) || $form_state->getValues()['helpertype'] == 0) {
          $form_state->setErrorByName('helpertype', 'Please enter your Helper Type');
        }
        break;

      case 2:
        if (empty($form_state->getValues()['primaryphone'])) {
          $form_state->setErrorByName('primaryphone', 'Please enter a phone number');
        }
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $phone = $form_state->getValues()['primaryphone'];
    $helperType = $form_state->get('page_values')['helpertype'];

    // Temporarily store values to be set later on form_alter.
    $tempstore = \Drupal::service('tempstore.private')->get('helperui');
    $tempstore->set('primaryphone', $phone);
    $tempstore->set('helpertype', $helperType);

    // Look for existing helper records with the same phone number.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'helper')
      ->condition('status', 1)
      ->condition('field_phone_pub', $phone)
      ->execute();

    // If there are nodes found, we will redirect the users to that page
    // else we will redirect users to /node/add/helper page and auto-fill
    // the helpertype and phone number fields.
    $path = (!empty($query)) ? '/node/' . $query[0] : '/node/add/helper';
    $url = Url::fromUserInput($path);
    $form_state->setRedirectUrl($url);
  }

}
