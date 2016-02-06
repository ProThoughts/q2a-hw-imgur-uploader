<?php
class hw_admin_page {
    function admin_form()
    {
        require_once QA_INCLUDE_DIR.'qa-util-sort.php';
        $saved=false;
        if (qa_clicked('SAVE_BUTTON')) {
            //qa_opt('hw_imgur_token', (int)qa_post_text('show_online_user_list_field'));
            qa_opt('hw_imgur_album', (qa_post_text('hw_imgur_album_field')=='') ? 'bld18' : qa_post_text('hw_imgur_album_field'));  //save album id
            qa_opt('hw_imgur_show_image_id', (int)qa_post_text('hw_imgur_show_image_id_field'));
            qa_opt('hw_enable_iflychat', (int)qa_post_text('hw_enable_iflychat_field'));
            $saved=true;
        }
        $form=array(
            'ok' => $saved ? qa_lang_html('hw_lang/change_ok') : null,

            'fields' => array(
                'question1' => array(
                    'label' => qa_lang_html('hw_lang/imgur_album_id'),
                    'type' => 'text',
                    'value' => qa_html(qa_opt('hw_imgur_album')),
                    'tags' => 'name="hw_imgur_album_field"',
                ),
                //seem not be used
                'question2' => array(
                    'label' => qa_lang_html('hw_lang/imgur_show_image_id'),
                    'type' => 'checkbox',
                    'value' => (int)qa_opt('hw_imgur_show_image_id'),
                    'tags' => 'name="hw_imgur_show_image_id_field"',
                ),
                'question3' => array(
                    'label' => qa_lang_html('hw_lang/enable_iflychat'),
                    'type' => 'checkbox',
                    'value' => (int)qa_opt('hw_enable_iflychat'),
                    'tags' => 'name="hw_enable_iflychat_field"',
                ),

            ),

            'buttons' => array(
                array(
                    'label' => qa_lang_html('hw_lang/save_button'),
                    'tags' => 'name="SAVE_BUTTON"',
                ),
            ),
        );

        return $form;
    }
}