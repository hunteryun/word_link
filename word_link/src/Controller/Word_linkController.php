<?php

namespace Hunter\word_link\Controller;

use Zend\Diactoros\ServerRequest;
use Psr\Http\Message\UploadedFileInterface;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class word_link.
 *
 * @package Hunter\word_link\Controller
 */
class Word_linkController {
  /**
   * word_link_list.
   *
   * @return string
   *   Return word_link_list string.
   */
  public function word_link_list(ServerRequest $request) {
    $parms = $request->getQueryParams();
    if(!isset($parms['page'])){
      $parms['page'] = 1;
    }
    $word_link_result = get_all_word_link($parms);

    return view('/admin/word_link-list.html', array('word_links' => $word_link_result['list'], 'pager' => $word_link_result['pager']));
  }

  /**
   * word_link_add.
   *
   * @return string
   *   Return word_link_add string.
   */
  public function word_link_add(ServerRequest $request) {
    if ($parms = $request->getParsedBody()) {
      $user = session()->get('admin');

      $wid = db_insert('word_link')
        ->fields(array(
          'words' => clean($parms['words']),
          'url' => clean($parms['url']),
          'status' => $parms['status'],
          'uid' => $user->uid,
          'created' => time(),
          'updated' => time(),
        ))
        ->execute();

      return hunter_form_submit($parms, 'word_link', $wid);
    }

    $form['words'] = array(
      '#type' => 'textfield',
      '#title' => '关键字',
      '#maxlength' => 255,
    );
    $form['url'] = array(
      '#type' => 'textfield',
      '#title' => '链接',
      '#maxlength' => 255,
    );
    $form['status'] = array(
      '#type' => 'radios',
      '#title' => '状态',
      '#default_value' => '1',
      '#options' => array(
        '1' => '启用',
        '0' => '禁用',
      ),
    );
    $form['save'] = array(
     '#type' => 'submit',
     '#value' => t('Save'),
     '#attributes' => array('lay-submit' => '', 'lay-filter' => 'word_linkAdd'),
    );

    return view('/admin/word_link-add.html', array('form' => $form));
  }

  /**
   * word_link_edit.
   *
   * @return string
   *   Return word_link_edit string.
   */
  public function word_link_edit($wid) {
      $word_link = get_word_link_byid($wid);

      $form['words'] = array(
        '#type' => 'textfield',
        '#title' => '关键字',
        '#default_value' => $word_link->words,
        '#maxlength' => 255,
      );
      $form['url'] = array(
        '#type' => 'textfield',
        '#title' => '链接',
        '#default_value' => $word_link->url,
        '#maxlength' => 255,
      );
      $form['status'] = array(
        '#type' => 'radios',
        '#title' => '状态',
        '#default_value' => $word_link->status,
        '#options' => array(
          '1' => '启用',
          '0' => '禁用',
        ),
      );

      $form['wid'] = array(
        '#type' => 'hidden',
        '#value' => $wid,
      );
      $form['save'] = array(
       '#type' => 'submit',
       '#value' => t('Save'),
       '#attributes' => array('lay-submit' => '', 'lay-filter' => 'word_linkUpdate'),
      );

      return view('/admin/word_link-edit.html', array('form' => $form, 'word_link' => $word_link, 'wid' => $wid));
  }

  /**
   * word_link_update.
   *
   * @return string
   *   Return word_link_update string.
   */
  public function word_link_update(ServerRequest $request) {
    if ($parms = $request->getParsedBody()) {
      $wid = $parms['wid'];
      $user = session()->get('admin');


       db_update('word_link')
         ->fields(array(
                                  'words' => clean($parms['words']),
                                               'url' => clean($parms['url']),
                                                    'status' => $parms['status'],
                        'uid' => $user->uid,
           'updated' => time(),
         ))
         ->condition('wid', $wid)
         ->execute();

       return hunter_form_submit($parms, 'word_link', true);
     }
     return false;
  }

  /**
   * word_link_del.
   *
   * @return string
   *   Return word_link_del string.
   */
  public function word_link_del($wid) {
    $result = db_delete('word_link')
            ->condition('wid', $wid)
            ->execute();

    if ($result) {
      return true;
    }

    return false;
  }

}
