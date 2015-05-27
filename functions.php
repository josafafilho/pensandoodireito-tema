<?php

// this adds jquery tooltip and styles it
function pensandoodireito_scripts() {
    wp_enqueue_script( 'pensandoodireto', get_stylesheet_directory_uri() . '/js/pensandoodireito.js' , array(), false, true );
}
add_action( 'admin_enqueue_scripts', 'pensandoodireito_scripts' );

//Script to ajax load more publicacoes
function publicacoes_scripts() {
  wp_enqueue_script( 'publicacoes', get_stylesheet_directory_uri() . '/js/publicacoes.js' , array(), false, true );
  $publicacoes_data = array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'paginaAtual' => 3,
    'ajaxgif' => get_template_directory_uri() . '/images/ajax-loader.gif'
  );

  wp_localize_script( 'publicacoes', 'publicacoes', $publicacoes_data );
}
add_action( 'wp_enqueue_scripts', 'publicacoes_scripts' );

/**
 * Registar post type publicação
 */
function publicacao_post_type() {

    $labels = array(
        'name'                => _x( 'Publicações', 'Post Type General Name', 'pensandooodireito' ),
        'singular_name'       => _x( 'Publicação', 'Post Type Singular Name', 'pensandooodireito' ),
        'menu_name'           => __( 'Publicações', 'pensandooodireito' ),
        'parent_item_colon'   => __( 'Publicação pai:', 'pensandooodireito' ),
        'all_items'           => __( 'Todas as publicações', 'pensandooodireito' ),
        'view_item'           => __( 'Ver publicação', 'pensandooodireito' ),
        'add_new_item'        => __( 'Adicionar publicação', 'pensandooodireito' ),
        'add_new'             => __( 'Adicionar nova', 'pensandooodireito' ),
        'edit_item'           => __( 'Editar Publicação', 'pensandooodireito' ),
        'update_item'         => __( 'Atualizar Publicação', 'pensandooodireito' ),
        'search_items'        => __( 'Buscar publicação', 'pensandooodireito' ),
        'not_found'           => __( 'Não enconrtado', 'pensandooodireito' ),
        'not_found_in_trash'  => __( 'Não encontrado na lixeira', 'pensandooodireito' ),
    );

    $supports = array(
        'title',
        'editor',
        'excerpt',
        'thumbnail',
        'comments',
        'trackbacks',
        'revisions',
        'custom-fields',
    );

    $args = array(
        'label'               => __( 'publicacao', 'pensandooodireito' ),
        'description'         => __( 'Publicações do Pensando o Direito', 'pensandooodireito' ),
        'labels'              => $labels,
        'supports'            => $supports,
        'taxonomies'          => array( 'category', 'post_tag' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'register_meta_box_cb' => 'add_publicacao_metaboxes', //Para adicionar novos campos
        'rewrite'             => array( 'slug' => 'publicacoes', 'with_front' => false),
    );

    // this adds jquery datepicker and styles it
    function dp_styles() {
        wp_enqueue_style('jquery-ui-datepicker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    }
    add_action('admin_print_styles', 'dp_styles');
    function dp_scripts() {
        wp_enqueue_script('jquery-ui-datepicker');
    }
    add_action( 'admin_enqueue_scripts', 'dp_scripts' );

    // Metaboxes adicionadas seguindo o tutorial: http://wptheming.com/2010/08/custom-metabox-for-post-type/
    // and also thanks to @sterndata at #wordpress irc channel for the help
    // http://wpbin.io/dfwy8d
    function add_publicacao_metaboxes() {
        // Adiciona um campo para data da oficial da publicação, independente da data do post.
        add_meta_box('wpt_publicacao_data','Dados da Publicação', 'wpt_publicacao_data', 'publicacao', 'side', 'high');
        // Adiciona um campo para anexar a versão as versões "web" e para Download da Publicação
        add_meta_box('wpt_publicacao_files','Arquivos da Publicação', 'wpt_publicacao_files', 'publicacao', 'side', 'high');
        // Adiciona um campo para anexar a versão para Download da Publicação
        //add_meta_box('wpt_publicacao_dld_version','Versão para download', 'wpt_publicacao_dld_version', 'publicacao', 'side', 'high');
    }

    //Geração do HTML das metaboxes
    function wpt_publicacao_data() {
        global $post;

        // Campo necessário para verificar origem do dado
        echo '<input type="hidden" name="publicacaometa_noncename" id="publicacaometa_noncename" value="' .
        wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

        // Recupera a variáveis, se já adicionadas
        $pub_date = get_post_meta($post->ID, 'pub_date', true);
        $pub_number = get_post_meta($post->ID, 'pub_number', true);

        // Imprime o campo para Número da Publicação
        echo '<p><label>Número da Publicação</label><input type="number" name="pub_number" value="' . $pub_number . '" class="" required /></p>';

        // Imprime o campo para Data da Publicação
        echo '<p><label>Data da Publicação</label><input type="text" name="pub_date" value="' . $pub_date . '" class="datePick" required /></p>';
    }

    //Geração do HTML para upload dos arquivos
    function wpt_publicacao_files(){
        global $post;

        $pub_web_file = get_post_meta($post->ID, 'pub_web_file', true);
        $pub_dld_file = get_post_meta($post->ID, 'pub_dld_file', true);

        // Recupera arquivos caso já tenha sido adicionados
        //TODO: HOW TO RETRIEVE ALREADY UPDATED FILES, SO I CAN UPDATE THE POST WITHOU UPLOADING FILES AGAIN?
        $html  = "<p><label>Versão Web</label>";
        if( $pub_web_file ) {
            $html .= "<br/><a href='" . $pub_web_file . "' target='_blank'>Arquivo Atual</a>";
        }
        $html .= "<input type='file' name='pub_web_file' id='pub_web_file' value='' size='25'/></p>";
        $html .= "<p><label>Versão para Download</label>";
        if( $pub_dld_file ) {
            $html .= "<br/><a href='" . $pub_dld_file . "' target='_blank'>Arquivo Atual</a>";
        }
        $html .= "<input type='file' name='pub_dld_file' id='pub_dld_file' value='' size='25'/></p>";
        echo $html;
    }

    // Save the Metabox Data
    function wpt_save_publicacao_meta($post_id, $post) {

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !isset($_POST['publicacaometa_noncename']) || !wp_verify_nonce( $_POST['publicacaometa_noncename'], plugin_basename(__FILE__) )) {
            return $post->ID;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ))
            return $post->ID;

        // OK, we're authenticated: we need to find and save the data
        // We'll put it into an array to make it easier to loop though.
        $publicacao_meta['pub_date'] = $_POST['pub_date'];
        $publicacao_meta['pub_number'] = $_POST['pub_number'];

        // Make sure the file array isn't empty
        if(!empty($_FILES['pub_web_file']['name']) && !empty($_FILES['pub_dld_file']['name'])) {

            // Setup the array of supported file types. In this case, it's just PDF.
            $supported_types = array('application/pdf');

            // Get the file type of the upload
            $arr_web_file_type = wp_check_filetype(basename($_FILES['pub_web_file']['name']));
            $uploaded_web_type = $arr_web_file_type['type'];
            $arr_dld_file_type = wp_check_filetype(basename($_FILES['pub_dld_file']['name']));
            $uploaded_dld_type = $arr_dld_file_type['type'];

            // Check if the type is supported. If not, throw an error.
            if( !in_array($uploaded_web_type, $supported_types) ) {
                //TOOD: Improve this message
                wp_die("O arquivo para web não é um PDF (formato permitido).");
            }
            if( !in_array($uploaded_dld_type, $supported_types) ){
                //TOOD: Improve this message
                wp_die("O arquivo para download não é um PDF (formato permitido).");
            }

            // Use the WordPress API to upload the file
            $upload_web = wp_upload_bits($_FILES['pub_web_file']['name'], null, file_get_contents($_FILES['pub_web_file']['tmp_name']));
            $upload_dld = wp_upload_bits($_FILES['pub_dld_file']['name'], null, file_get_contents($_FILES['pub_dld_file']['tmp_name']));

            if(isset($upload_web['error']) && $upload_web['error'] != 0) {
                wp_die('Erro ao salvar arquivo para Web. O erro foi: ' . $upload['error']);
            } else {
                $publicacao_meta['pub_web_file'] = $upload_web['url'];
            }
            if(isset($upload_dld['error']) && $upload_dld['error'] != 0) {
                wp_die('Erro ao salvar arquivo para Download. O erro foi: ' . $upload['error']);
            } else {
                $publicacao_meta['pub_dld_file'] = $upload_dld['url'];
            }
        }

        // Add values of $events_meta as custom fields
        foreach ($publicacao_meta as $key => $value) { // Cycle through the $events_meta array!
            if( $post->post_type == 'revision' ) return; // Don't store custom data twice
            $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
            if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
        }

    }
    add_action('save_post', 'wpt_save_publicacao_meta', 1, 2); // save the custom fields

    function pensandoodireito_update_edit_form() {
        echo ' enctype="multipart/form-data"';
    } // end update_edit_form
    add_action('post_edit_form_tag', 'pensandoodireito_update_edit_form');

    //Registrando o post-type Publicação
    register_post_type( 'publicacao', $args );

}

// Iniciarlizar publicação.
add_action( 'init', 'publicacao_post_type', 0 );

// Register Custom Post Type
function destaque_post_type() {

    $labels = array(
        'name'                => _x( 'Destaques', 'Post Type General Name', 'pensandoodireito' ),
        'singular_name'       => _x( 'Destaque', 'Post Type Singular Name', 'pensandoodireito' ),
        'menu_name'           => __( 'Destaques', 'pensandoodireito' ),
        'name_admin_bar'      => __( 'Destaques', 'pensandoodireito' ),
        'parent_item_colon'   => __( 'Destaque pai:', 'pensandoodireito' ),
        'all_items'           => __( 'Todos destaques', 'pensandoodireito' ),
        'add_new_item'        => __( 'Adicionar novo destaque', 'pensandoodireito' ),
        'add_new'             => __( 'Adicionar novo', 'pensandoodireito' ),
        'new_item'            => __( 'Novo destaque', 'pensandoodireito' ),
        'edit_item'           => __( 'Editar destaque', 'pensandoodireito' ),
        'update_item'         => __( 'Atualizar destaque', 'pensandoodireito' ),
        'view_item'           => __( 'Ver destaque', 'pensandoodireito' ),
        'search_items'        => __( 'Buscar destaque', 'pensandoodireito' ),
        'not_found'           => __( 'Não encontrado', 'pensandoodireito' ),
        'not_found_in_trash'  => __( 'Não encontrado na lixeira', 'pensandoodireito' ),
    );
    $args = array(
        'label'               => __( 'destaque', 'pensandoodireito' ),
        'description'         => __( 'Descrição do Destaque', 'pensandoodireito' ),
        'labels'              => $labels,
        'supports'            => array( 'title' ), //'editor',
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'register_meta_box_cb' => 'add_destaque_metaboxes', //Para adicionar novos campos
        'rewrite'             => array( 'slug' => 'destaques', 'with_front' => false),
    );

    // Metaboxes adicionadas seguindo o tutorial: http://wptheming.com/2010/08/custom-metabox-for-post-type/
    // and also thanks to @sterndata at #wordpress irc channel for the help
    // http://wpbin.io/dfwy8d
    function add_destaque_metaboxes() {
        // Adiciona um campo para upload de vídeo ou imagem do destaque.
        add_meta_box('wpt_destaque_ativo', 'Destaque ativo', 'wpt_destaque_ativo', 'destaque', 'side', 'high');
        //add_meta_box('wpt_destaque_preview', 'Previsão do destaque', 'wpt_destaque_preview', 'destaque', 'normal', 'high');
        add_meta_box('wpt_destaque_link', 'Link do destaque', 'wpt_destaque_link', 'destaque', 'normal', 'high');
        add_meta_box('wpt_destaque_modelo', 'Modelo do destaque', 'wpt_destaque_modelo', 'destaque', 'normal', 'high');
        add_meta_box('wpt_destaque_midia', 'Mídia do Destaque', 'wpt_destaque_midia', 'destaque', 'normal', 'high');
        add_meta_box('wpt_destaque_texto', 'Texto do Destaque', 'wpt_destaque_texto', 'destaque', 'normal', 'high');
    }

    function wpt_destaque_ativo($post) {
        echo '<input type="hidden" name="destaquemeta_noncename" id="destaquemeta_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
        wp_enqueue_script( 'pensandoodireto', get_stylesheet_directory_uri() . '/js/pensandoodireito.js' , array(), false, true );
        wp_enqueue_media();

        $destaque_ativo = get_post_meta($post->ID, 'destaque_ativo', true);
        echo '<input type="radio" name="destaque_ativo" id="destaque_ativo" ';
        if ($destaque_ativo != 0) {
            echo 'checked';
        }
        echo ' value="1"/> Mostrar<br/>';
        echo '<input type="radio" name="destaque_ativo" id="destaque_ativo" ';
        if ($destaque_ativo == 0) {
            echo 'checked';
        }
        echo ' value="0"/> Não mostrar';

    }

    function wpt_destaque_link($post) {
        $link = get_post_meta($post->ID, 'destaque_link', true);

        $link_html = '<label for="destaque_link">';
        $link_html .= 'Digite abaixo o link para o qual o destaque irá direcionar<br/>';
        $link_html .= 'Link: <input type="text" name="destaque_link" id="destaque_link" size="80" value="' . $link . '"/>';
        $link_html .= '</label>';

        echo $link_html;

    }

    // Gera, na área de administração, o preview de como ficará o destaque
    function wpt_destaque_preview($post) {

        $midia_destaque = get_post_meta($post->ID, 'midia_destaque', true);
        $modelo_destaque = get_post_meta($post->ID, 'modelo_destaque', true);

        $preview_html .= '<label for="midia_destaque">';

        if ($modelo_destaque != 'img_texto' && $modelo_destaque != 'img_full') {
            $tipo_midia_img = false;
        }

        if( $midia_destaque && $midia_destaque != '' ) {

            if ($modelo_destaque == 'img_full') {
                $preview_html .= '<img style="width: 1024px; height: 390px;" src="' . $midia_destaque . '"/>';
            } else if ($modelo_destaque == 'img_texto'){
                $preview_html .= '<img style="width: 640px; height: 390px;" src="' . $midia_destaque . '"/>';
            } else if ($modelo_destaque == 'video_full') {
                echo do_shortcode('[youtube id="' . getYoutubeIdFromUrl($midia_destaque) . '"]');
            }
        }

        echo $preview_html;
    }

    function wpt_destaque_modelo($post) {

        $modelo_destaque = get_post_meta($post->ID, 'modelo_destaque', true);

        //Seletor de modelo de Destaque
        echo '<label for="modelo_destaque">Selecione o modelo de destaque que você deseja</label><br/>';
        echo '<div style="width: 23%; min-width: 185px; padding: 5px; display: inline-block; text-align: center;">';
        echo '<img src="' . get_stylesheet_directory_uri() . '/images/destaque_img_texto.png" alt="Imagem com texto à direita" title="Imagem com texto à direita"/><br/>';
        echo '<input type="radio" name="modelo_destaque" id="modelo_destaque" onchange="destaque_controla_midia(this.value)" value="img_texto"';
            if ($modelo_destaque == 'img_texto' || $modelo_destaque == ''){ echo ' checked'; }
            echo '/>Imagem com texto à direita<br/>(640px x 390px)';
        echo '</div>';
        echo '<div style="width: 23%; min-width: 185px; padding: 5px; display: inline-block; text-align: center;">';
        echo '<img src="' . get_stylesheet_directory_uri() . '/images/destaque_video_texto.png" alt="Vídeo com texto à direita" title="Vídeo com texto à direita"/><br/>';
        echo '<input type="radio" name="modelo_destaque" id="modelo_destaque" onchange="destaque_controla_midia(this.value)" value="video_texto"';
            if ($modelo_destaque == 'video_texto'){ echo ' checked'; }
            echo '/>Vídeo com texto à direita<br/>(640px x 390px)';
        echo '</div>';
        echo '<div style="width: 23%; min-width: 185px; padding: 5px; display: inline-block; text-align: center;">';
        echo '<img src="' . get_stylesheet_directory_uri() . '/images/destaque_img_full.png" alt="Apenas imagem" title="Apenas imagem"/><br/>';
        echo '<input type="radio" name="modelo_destaque" id="modelo_destaque" onchange="destaque_controla_midia(this.value)" value="img_full"';
            if ($modelo_destaque == 'img_full'){ echo ' checked'; }
            echo '/>Apenas imagem Full Width<br/>(1024px x 390px)';
        echo '</div>';
        echo '<div style="width: 23%; min-width: 185px; padding: 5px; display: inline-block; text-align: center;">';
        echo '<img src="' . get_stylesheet_directory_uri() . '/images/destaque_video_full.png" alt="Apenas vídeo" title="Apenas vídeo"/><br/>';
        echo '<input type="radio" name="modelo_destaque" id="modelo_destaque" onchange="destaque_controla_midia(this.value)" value="video_full"';
            if ($modelo_destaque == 'video_full'){ echo ' checked'; }
            echo '/>Apenas vídeo Full Width<br/>(1024px x 390px)';
        echo '</div>';
    }

    function wpt_destaque_midia($post) {

        $midia_destaque = get_post_meta($post->ID, 'midia_destaque', true);
        $modelo_destaque = get_post_meta($post->ID, 'modelo_destaque', true);

        $midia_html = '';

        $tipo_midia_img = True;
        $destaque_com_texto = True;

        if ( $modelo_destaque == 'video_texto' || $modelo_destaque == 'video_full' ) {
            $tipo_midia_img = False;
        }
        if ( $modelo_destaque == 'video_full' || $modelo_destaque == 'img_full' ) {
            $destaque_com_texto = False;
        }

        $img = 'block';
        $video = 'none';
        if ( !$tipo_midia_img ) {
            $img = 'none';
            $video = 'block';
        }

        $img_formats_patterns = ['#\.jpg$#', '#\.png$#', '#\.jpeg#'];

        $midia_html .= '<input id="upload_image_button" class="button midia_imagem" type="button" value="Selecione a Imagem" style="display:' . $img . ';"/>';
        $midia_html .= '<p class="midia_video" style="display:' . $video . ';">Coloque a url do vídeo no youtube na caixa abaixo.</p>';
        $midia_html .= '<input id="midia_destaque" class="midia_video" type="text" size="80" name="midia_destaque" value="' . $midia_destaque . '" style="display:' . $video . ';" />';
        $midia_html .= '<br/>';
        $midia_html .= '<img style="width: 300px;" class="midia_imagem" style="display:' . $img  . '" id="img_preview" src="';
        if ($tipo_midia_img) {
            foreach ($img_formats_patterns as $pattern) { // Cycle through the $events_meta array!
              if ( preg_match( $pattern, $midia_destaque ) ) {
                   $midia_html .= $midia_destaque;
              }
            }
        }
        $midia_html .= '"/>';

        if( $midia_destaque && $midia_destaque != '' ) {
            if (!$tipo_midia_img && getYoutubeIdFromUrl($midia_destaque) ) {
                echo do_shortcode('[youtube id="' . getYoutubeIdFromUrl($midia_destaque) . '"]');
            }
        }
        $midia_html .= '</label>';
        echo $midia_html;
    }


    function wpt_destaque_texto($post) {

        $destaque_texto = get_post_meta($post->ID, 'destaque_texto', true);
        $modelo_destaque = get_post_meta($post->ID, 'modelo_destaque', true);

        $texto = 'block';
        if ( $modelo_destaque == 'video_full' || $modelo_destaque == 'img_full' ) {
            $texto = 'none';
        }

        echo '<label class="destaque_texto" for="destaque_texto" style="display: ' . $texto . '">Digite o texto a ser utilizado na chamada.<br/>';
        echo '<textarea name="destaque_texto" id="destaque_texto" rows="3" cols="80" size="100px" maxlength="230">';
        echo $destaque_texto;
        echo '</textarea></label>';

    }

    add_action('save_post', 'wpt_save_destaque_meta', 1, 2); // save the custom fields
    // Save the Metabox Data
    function wpt_save_destaque_meta($post_id, $post) {

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !isset($_POST['destaquemeta_noncename']) || !wp_verify_nonce( $_POST['destaquemeta_noncename'], plugin_basename(__FILE__) )) {
            return $post->ID;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID )) {
            return $post->ID;
        }

        $destaque_meta = get_post_meta($post->ID);

        if ( isset( $_REQUEST['modelo_destaque'] ) ) {
            $destaque_meta['modelo_destaque'] = $_REQUEST['modelo_destaque'];
        }

        if ( isset( $_REQUEST['midia_destaque']  ) && $_REQUEST['midia_destaque'] != '' ) {
            $destaque_meta['midia_destaque'] = $_REQUEST['midia_destaque'];
        }

        if ( isset( $_REQUEST['destaque_ativo'] ) ) {
            $destaque_meta['destaque_ativo'] = $_REQUEST['destaque_ativo'];
        }

        if ( isset( $_REQUEST['destaque_texto'] ) && $_REQUEST['destaque_texto'] != '' ) {
            $destaque_meta['destaque_texto'] = $_REQUEST['destaque_texto'];
        }

        if ( isset( $_REQUEST['destaque_link'] ) && $_REQUEST['destaque_link'] != '' ) {
            $destaque_meta['destaque_link'] = $_REQUEST['destaque_link'];
        }

        // Add values of $events_meta as custom fields
        foreach ($destaque_meta as $key => $value) { // Cycle through the $events_meta array!
            if( $post->post_type == 'revision' ) return; // Don't store custom data twice
            $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
            if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
        }

    }
    add_action('post_edit_form_tag', 'pensandoodireito_update_edit_form');

    // Add to admin_init function
    add_filter('manage_edit-destaque_columns', 'add_new_destaque_columns');
    function add_new_destaque_columns($destaque_columns) {
        $new_columns['cb'] = '<input type="checkbox" />';
        $new_columns['title'] = _x('Nome do Destaque', 'column name');
        $new_columns['modelo_destaque'] = 'Modelo de Destaque';
        $new_columns['destaque_ativo'] = 'Ativo?';
        $new_columns['date'] = _x('Date', 'column name');

        return $new_columns;
    }


    add_action( 'manage_destaque_posts_custom_column', 'my_manage_destaque_columns', 10, 2 );

    function my_manage_destaque_columns( $column, $post_id ) {
        global $post;

        switch( $column ) {

            case 'modelo_destaque' :

                /* Get the post meta. */
                $modelo = get_post_meta( $post_id, 'modelo_destaque', true );

                if ( empty( $modelo ) )
                    echo 'Desconhecido';
                else if ( $modelo == 'img_texto' )
                    printf('Imagem e Texto');
                else if ( $modelo == 'video_texto' )
                    printf('Vídeo e Texto');
                else if ( $modelo == 'img_full' )
                    printf('Apenas Imagem');
                else
                    printf('Apenas Vídeo');

                break;

            case 'destaque_ativo' :

                $ativo = get_post_meta( $post_id, 'destaque_ativo', true );

                if ( empty( $ativo ) || $ativo == '' || $ativo == 0 || $ativo == false || $ativo == "0") {
                    echo 'Desativado';
                } else {
                    echo 'Ativo';
                }

                break;

            /* Just break out of the switch statement for everything else. */
            default :
                break;
        }
    }


    register_post_type( 'destaque', $args );

}
// Hook into the 'init' action
add_action( 'init', 'destaque_post_type', 0 );

/*add_action( 'widgets_init', 'pensandoodireito_widgets_init' );
function pensandoodireito_widgets_init()
{
    register_sidebar(array(
        'name' => __('Outras Publicações', 'pensandoodireito'),
        'id' => 'pensando-outras-publicacoes',
        'description' => '',
        'class' => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>'));

}*/

//Function used to limit the 'publicacao' title when printing it on the page.
function the_title_limit($length, $replacer = '...') {
  $string = the_title('','',FALSE);
  if(strlen($string) > $length) {
    $words = explode(" ", $string);
    $word_index = 1;
    $output = $words[0];
    while (strlen($output) + strlen($words[$word_index]) < $length - 4) {
        $output .= ' ' . $words[$word_index];
        $word_index += 1;
    }
    $output .= ' ...';
  } else {
    $output = $string;
  }
  echo $output;
}

/**
 * Função ajax pra paginação infinita
 */
function publicacoes_paginacao_infinita(){
    $paged = $_POST['paged'];
    $destaqueID = $_POST['destaqueID'];

    $pubs_args = array(
      'paged' => $paged,
      'post_type' => 'publicacao',
      'posts_per_page' => 4,
      'post__not_in' => array($destaqueID ),
    );

    if ( !empty($_POST) ) {
      if ( isset($_POST['filter-name']) ) {
        $pubs_args['s'] = $_POST['filter-name'];
      }
      if ( isset($_POST['sort-option']) ) {
        switch ($_POST['sort-option']) {
          case 'pub_number':
            $pubs_args['orderby'] = 'meta_value_num';
            $pubs_args['meta_key'] = 'pub_number';
            break;
          case 'title':
            $pubs_args['orderby'] = 'title';
            break;
        }
      } else {
        $pubs_args['orderby'] = 'meta_value_num';
        $pubs_args['meta_key'] = 'pub_number';
      }
      if ( isset($_POST['sort-order']) ) {
        $pubs_args['order'] = $_POST['sort-order'];
      } else {
        $pubs_args['order'] = 'DESC';
      }
    }

    $publicacoes = new WP_Query ($pubs_args);

    $counter = 0;
    if ($publicacoes->have_posts()) {
        while ($publicacoes->have_posts()) {
          $publicacoes->the_post();
            if ($counter % 4 == 0) { ?> <div class="row"> <?php }
            get_template_part('publicacao','card');
            if (($counter + 1) % 4 == 0) { ?> </div> <?php }
            $counter++;
        }
    }

    exit;
}

add_action('wp_ajax_publicacoes_paginacao_infinita', 'publicacoes_paginacao_infinita');
add_action('wp_ajax_nopriv_publicacoes_paginacao_infinita', 'publicacoes_paginacao_infinita');

// Função que cria páginas (pages),
//   em especial focando nos 'endpoints'
function pd_create_pages() {
  // Página "o que é"
  pd_create_page( array('titulo' => 'O que é?', 'nome' => 'o-que-e') );

  // Página "parceiros"
  pd_create_page( array(
   'titulo' => 'Parceiros',
   'nome' => 'parceiros',
   'conteudo' => '<p>Parcerias estabelecidas pela SAL/MJ com instituições acadêmicas, centros de pesquisa, ONG’s e também com demais entes públicos para a consecução dos objetivos próprios do Projeto Pensando o Direito.</p>
        <ul id="lista-parceiros">
        <li>Associação dos Advogados de São Paulo</li>
        <li>Associação dos Juízes para a Democracia</li>
        <li>Associação Nacional dos Defensores Públicos (Anadep)</li>
        <li>Associações de defensoria</li>
        <li>Central dos Movimentos Populares</li>
        <li>Centro Feminista de Estudos e Assessoria (CFEMEA)</li>
        <li>Comitê Gestor da Internet no Brasil (CGI.br)</li>
        <li>Conselho Nacional de Justiça (CNJ)</li>
        <li>Conselho Nacional do Ministério Público</li>
        <li>Cultura Digital & Democracia</li>
        <li>Defensoria Pública</li>
        <li>Defensoria Pública da União</li>
        <li>Defensoria Pública do Estado de São Paulo</li>
        <li>Departamento Jurídico XI de Agosto</li>
        <li>Escritório Modelo da PUC-SP</li>
        <li>Estratégia Nacional de Combate à Lavagem de Dinheiro (ENCCLA)</li>
        <li>Estratégia Nacional de Justiça e Segurança Pública (Enasp)</li>
        <li>Federação Nacional de Estudantes de Direito (Fened)</li>
        <li>Fórum Justiça</li>
        <li>Instituo Brasileiro de Direito Processual</li>
        <li>Instituto Beta para Internet e Democracia (Ibidem)</li>
        <li>Instituto Brasileiro de Ciências Criminais (IBCCrim)</li>
        <li>Instituto Brasileiro de Defesa do Consumidor (Idec)</li>
        <li>Instituto Brasileiro de Direito de Família</li>
        <li>Instituto Brasileiro de Direito Urbanístico (IBDU)</li>
        <li>Instituto Carioca de Criminologia (ICC)</li>
        <li>Instituto Pólis</li>
        <li>Instituto Scarance</li>
        <li>Instituto Sou da Paz</li>
        <li>InternetLab</li>
        <li>Intervozes (coletivo)</li>
        <li>Movimento do Ministério Público Democrático (MPD)</li>
        <li>Movimento dos Trabalhadores Rurais Sem-Terra (MST)</li>
        <li>Movimento dos Trabalhadores Sem-Teto (MTST)</li>
        <li>Oboré (empresa)</li>
        <li>Oficina de Imagens</li>
        <li>Ordem dos Advogados do Brasil (OAB)</li>
        <li>Organização Internacional de Polícia Criminal (Interpol)</li>
        <li>Pastoral Carcerária</li>
        <li>Projeto Cala-boca já morreu</li>
        <li>Proteste - Associação Brasileira de Defesa do Consumidor</li>
        <li>Rede Nacional de Advogados e Advogados Populares (Renap)</li>
        <li>Sociedade Brasileira de Direito Público (SBDP)</li>
        <li>Terra de Direitos</li>
        <li>União dos Movimentos de Moradia de São Paulo</li>
        <li>Via Campesina</li>
        <li>World Wide Web Consortium (W3C)</li>
        </ul>'
  ) );

}

// Chama a função apenas quando há troca de tema
//   fundamentalmente quando o tema é ativado (e também desativado)
add_action('after_switch_theme', 'pd_create_pages');





