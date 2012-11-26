<?php
if (!class_exists("misamee_themed_form_widget") && class_exists("GFWidget")) {
	class misamee_themed_form_widget extends GFWidget
	{
		function misamee_themed_form_widget()
		{
			$this->WP_Widget('misamee_themed_form_widget', 'Themed Form',
				array('classname' => 'misamee_themed_form_widget',
					'description' => __('Themed Gravity Forms Widget', misamee_gf_themes::$localizationDomain)),
				array('width' => 200,
					'height' => 250,
					'id_base' => 'misamee_themed_form_widget')
			);
		}

		function widget($args, $instance)
		{
			/** @var $before_widget string */
			/** @var $before_title string */
			/** @var $after_title string */
			/** @var $after_widget string */

			extract($args);
			echo $before_widget;
			$title = apply_filters('widget_title', $instance['title']);

			if ($title)
				echo $before_title . $title . $after_title;

			//setting tabindex based on configured value
			if (is_numeric($instance['tabindex'])) {
				add_filter("misamee_themed_form_tabindex_{$instance['form_id']}", create_function("", "return {$instance['tabindex']};"));
			}

			$form = RGFormsModel::get_form_meta($instance['form_id']);

			$attributes = array(
				'title' => $instance['showtitle'] ? $form['title'] : '',
				'description' => $instance['showtitle'] ? $form['description'] : '',
				'id' => $instance['form_id'],
				'ajax' => ($instance['ajax'] == 1),
				'tabindex' => $instance['tabindex'],
				'action' => 'theme',
				'themename' => $instance['themed_template']
			);

			/*            echo '<pre>';
						print_r($attributes);
						echo '</pre>';*/
			$form_markup = str_replace("\\\"", "\"", RGForms::parse_shortcode($attributes));

			//display form
			echo $form_markup;
			echo $after_widget;
		}

		function update($new_instance, $old_instance)
		{
			$instance = $old_instance;
			$instance["title"] = strip_tags($new_instance["title"]);
			$instance["form_id"] = $new_instance["form_id"];
			$instance["showtitle"] = $new_instance["showtitle"];
			$instance["ajax"] = $new_instance["ajax"];
			$instance["disable_scripts"] = $new_instance["disable_scripts"];
			$instance["showdescription"] = $new_instance["showdescription"];
			$instance["tabindex"] = $new_instance["tabindex"];
			$instance["themed_template"] = $new_instance["themed_template"];

			return $instance;
		}

		function form($instance)
		{

			$instance = wp_parse_args((array)$instance, array('title' => __("Contact Us", misamee_gf_themes::$localizationDomain), 'tabindex' => '1'));
			?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title", misamee_gf_themes::$localizationDomain); ?>:</label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('form_id'); ?>"><?php _e("Select a Form", misamee_gf_themes::$localizationDomain); ?>:</label>
            <select id="<?php echo $this->get_field_id('form_id'); ?>" name="<?php echo $this->get_field_name('form_id'); ?>" style="width:90%;">
				<?php
				$forms = RGFormsModel::get_forms(1, "title");
				foreach ($forms as $form) {
					$selected = '';
					if ($form->id == rgar($instance, 'form_id'))
						$selected = ' selected="selected"';
					echo '<option value="' . $form->id . '" ' . $selected . '>' . $form->title . '</option>';
				}
				?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('themed_template'); ?>"><?php _e("Select a Template", misamee_gf_themes::$localizationDomain); ?>:</label>
            <select id="<?php echo $this->get_field_id('themed_template'); ?>" name="<?php echo $this->get_field_name('themed_template'); ?>" style="width:90%;">
				<?php
				$templates = misamee_themed_form::misamee_themed_form_themes();
				foreach ($templates as $name => $templateData) {
					$selected = '';
					if ($name == rgar($instance, 'themed_template'))
						$selected = ' selected="selected"';
					echo '<option value="' . $name . '" ' . $selected . '>' . $name . '</option>';
				}
				?>
            </select>
        </p>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('showtitle'); ?>" id="<?php echo $this->get_field_id('showtitle'); ?>" <?php checked(rgar($instance, 'showtitle')); ?> value="1"/> <label
                for="<?php echo $this->get_field_id('showtitle'); ?>"><?php _e("Display form title", misamee_gf_themes::$localizationDomain); ?></label><br/>
            <input type="checkbox" name="<?php echo $this->get_field_name('showdescription'); ?>" id="<?php echo $this->get_field_id('showdescription'); ?>" <?php checked(rgar($instance, 'showdescription')); ?> value="1"/> <label
                for="<?php echo $this->get_field_id('showdescription'); ?>"><?php _e("Display form description", misamee_gf_themes::$localizationDomain); ?></label><br/>
        </p>
        <p>
            <a href="javascript: var obj = jQuery('.gf_widget_advanced'); if(!obj.is(':visible')) {var a = obj.show('slow');} else {var a = obj.hide('slow');}"><?php _e("advanced options", misamee_gf_themes::$localizationDomain); ?></a>
        </p>
        <p class="gf_widget_advanced" style="display:none;">
            <input type="checkbox" name="<?php echo $this->get_field_name('ajax'); ?>" id="<?php echo $this->get_field_id('ajax'); ?>" <?php checked(rgar($instance, 'ajax')); ?> value="1"/> <label
                for="<?php echo $this->get_field_id('ajax'); ?>"><?php _e("Enable AJAX", misamee_gf_themes::$localizationDomain); ?></label><br/>
            <input type="checkbox" name="<?php echo $this->get_field_name('disable_scripts'); ?>" id="<?php echo $this->get_field_id('disable_scripts'); ?>" <?php checked(rgar($instance, 'disable_scripts')); ?> value="1"/> <label
                for="<?php echo $this->get_field_id('disable_scripts'); ?>"><?php _e("Disable script output", misamee_gf_themes::$localizationDomain); ?></label><br/>
            <label for="<?php echo $this->get_field_id('tabindex'); ?>"><?php _e("Tab Index Start", misamee_gf_themes::$localizationDomain); ?>: </label>
            <input id="<?php echo $this->get_field_id('tabindex'); ?>" name="<?php echo $this->get_field_name('tabindex'); ?>" value="<?php echo rgar($instance, 'tabindex'); ?>" style="width:15%;"/><br/>
            <span style="font-size: small;"><?php _e("If you have other forms on the page (i.e. Comments Form), specify a higher tabindex start value so that your Gravity Form does not end up with the same tabindices as your other forms. To disable the tabindex, enter 0 (zero).", misamee_gf_themes::$localizationDomain); ?></span>
        </p>

		<?php
		}
	}
}

?>