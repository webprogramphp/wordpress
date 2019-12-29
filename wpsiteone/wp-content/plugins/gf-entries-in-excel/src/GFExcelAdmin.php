<?php

namespace GFExcel;

use GFAddOn;
use GFCommon;
use GFExcel\Action\CountDownloads;
use GFExcel\Field\ProductField;
use GFExcel\Field\SeparableField;
use GFExcel\Renderer\PHPExcelMultisheetRenderer;
use GFExcel\Renderer\PHPExcelRenderer;
use GFExcel\Repository\FieldsRepository;
use GFExcel\Repository\FormsRepository;
use GFExcel\Shorttag\DownloadUrl;
use GFFormsModel;

class GFExcelAdmin extends GFAddOn
{
    const BULK_DOWNLOAD = 'gfexcel_download';

    private static $_instance = null;

    protected $_min_gravityforms_version = "2.0";

    protected $_capabilities_form_settings = ['gravityforms_export_entries'];

    /** @var FormsRepository micro cache */
    private $repository;

    /** @var string  micro cache for file name */
    private $_file = '';

    /**
     * @return string
     */
    public function plugin_settings_icon()
    {
        return '<i class="fa fa-table"></i>';
    }

    public function __construct()
    {
        $this->_version = GFExcel::$version;
        $this->_title = __(GFExcel::$name, GFExcel::$slug);
        $this->_short_title = __(GFExcel::$shortname, GFExcel::$slug);
        $this->_slug = GFExcel::$slug;

        $this->registerActions();
        parent::__construct();
    }

    /**
     * Set minimum requirements to prevent bugs when using older versions, or missing dependencies
     * @return array
     */
    public function minimum_requirements()
    {
        return [
            'php' => [
                'version' => '5.6',
                'extensions' => [
                    'zip', 'ctype', 'dom', 'zlib', 'xml',
                ],
            ]
        ];
    }

    public function render_uninstall()
    {
        return null;
    }

    public function plugin_settings_fields()
    {
        return [[
            'description' => $this->plugin_settings_description(),
            'fields' => [[
                'name' => 'field_separate',
                'label' => esc_html__('Multiple columns', GFExcel::$slug),
                'type' => 'checkbox',
                'choices' => [[
                    'label' => esc_html__('Split multi-fields (name, address) into multiple columns', GFExcel::$slug),
                    'name' => SeparableField::SETTING_KEY,
                    // backwards compatible with last known setting
                    'default_value' => static::get_instance()->get_plugin_setting('field_address_split_enabled')
                ]]
            ], [
                'name' => 'notes',
                'label' => esc_html__('Notes', 'gravityforms'),
                'type' => 'checkbox',
                'choices' => [[
                    'label' => esc_html__('Enable notes by default', GFExcel::$slug),
                    'name' => 'notes_enabled',
                    'default_value' => false,
                ]]
            ], [
                'name' => 'sections',
                'label' => esc_html__('Sections', GFExcel::$slug),
                'type' => 'checkbox',
                'choices' => [[
                    'label' => esc_html__('Enable (empty) section column', GFExcel::$slug),
                    'name' => 'sections_enabled',
                    'default_value' => false,
                ]]
            ], [
                'name' => 'fileuploads',
                'label' => esc_html__('File uploads', GFExcel::$slug),
                'type' => 'checkbox',

                'choices' => [[
                    'label' => esc_html__('Enable file upload columns', GFExcel::$slug),
                    'name' => 'fileuploads_enabled',
                    'default_value' => true,
                ]]
            ], [
                'name' => 'hyperlinks',
                'label' => esc_html__('Hyperlinks', GFExcel::$slug),
                'type' => 'checkbox',

                'choices' => [[
                    'label' => esc_html__('Enable hyperlinks on url-only columns', GFExcel::$slug),
                    'name' => 'hyperlinks_enabled',
                    'default_value' => true,
                ]]
            ], [
                'name' => 'products_price',
                'label' => esc_html__('Product fields', GFExcel::$slug),
                'type' => 'checkbox',

                'choices' => [[
                    'label' => esc_html__('Export prices as numeric fields, without currency symbol ($)', GFExcel::$slug),
                    'name' => ProductField::SETTING_KEY,
                    'default_value' => false,
                ]]
            ]],
        ], [
            'fields' => [
                [
                    'name' => 'enabled_metafields',
                    'label' => esc_html__('Enabled meta fields', GFExcel::$slug),
                    'description' => esc_html__('Select all meta fields that are enabled by default. Once you\'ve saved your form, these settings will not do anything any more.', GFExcel::$slug),
                    'type' => 'checkbox',

                    'choices' => $this->meta_fields(),
                ]
            ]
        ]];
    }

    public function init_admin()
    {
        parent::init_admin();

        add_action("bulk_actions-toplevel_page_gf_edit_forms", [$this, "bulk_actions"], 10, 2);
        add_action("wp_loaded", [$this, 'handle_bulk_actions']);
        add_action("admin_enqueue_scripts", [$this, "register_assets"]);
    }

    public function init()
    {
        parent::init();

        if ($form = $this->get_current_form()) {
            if (isset($_GET['gf_action'])) {
                // trigger action
                do_action('gfexcel_action_' . trim(strtolower((string) $_GET['gf_action'])), $form['id'], $this);
                // redirect back to same page without the action
                $url = ($_SERVER['PHP_SELF'] ?: '') . '?' . http_build_query(array_filter(array_merge($_GET, ['gf_action' => null])));
                wp_redirect($url);
                return;
            }

            $this->repository = new FormsRepository($form['id']);
        }

        add_action('gform_notification', [$this, 'handle_notification'], 10, 3);
        add_action('gform_after_email', [$this, 'remove_temporary_file'], 10, 13);
        add_filter('plugin_row_meta', [__CLASS__, 'plugin_row_meta'], 10, 2);
        add_filter('plugin_action_links', [__CLASS__, 'plugin_action_links'], 10, 2);
        add_filter('gform_form_actions', [__CLASS__, 'gform_form_actions'], 10, 2);
    }

    public function render_settings($sections)
    {
        parent::render_settings($sections);
        ?>
        <div class="hr-divider"></div>

        <a name="help-me-out"></a>
        <h3>
            <span><i class="fa fa-exclamation-circle"></i> <?php esc_html_e('Help me out!', GFExcel::$slug); ?></span>
        </h3>

        <p>
            <?php
            esc_html_e('I honestly ❤️ developing this plugin. It\'s fun, I get some practice, and I want to give back to the open-source community. But a good plugin, is a plugin that is constantly being updated and getting better. And I need your help to achieve this!', GFExcel::$slug);
            ?>
        </p>
        <p>
            <?php
            printf(' ' . esc_html__('If you find a bug 🐞 or need a feature 💡, %slet me know%s! I\'m very open to suggestions and ways to make the plugin more accessible.', GFExcel::$slug), '<a href="https://wordpress.org/support/plugin/gf-entries-in-excel" target="_blank">', '</a>');
            ?>
        </p>
        <p>
            <?php
            printf(' ' . esc_html__('If you like the plugin, let me know, and maybe more important; 📣 %slet others know%s! We already have %s active users. Let\'s get to %s by spreading the news! Be the first to know about updates by %sfollowing me on twitter%s.', GFExcel::$slug), '<a href="https://wordpress.org/support/plugin/gf-entries-in-excel/reviews/#new-post" target="_blank">', '</a>', $this->getUsageCount(), $this->getUsageTarget(), '<a href="https://twitter.com/doekenorg" target="_blank">', '</a>');
            ?>
        </p>
        <p>
            <?php
            esc_html_e('Also, If you ❤️ the plugin, and it helps you a lot, please consider making a small donation 💰 and buy me a beer 🍺.', GFExcel::$slug);
            ?>
        </p>
        <p>
            <a class="button button-cta" href="https://paypal.me/doekenorg"
               target="_blank"><?php _e('Make a donation', GFExcel::$slug); ?></a>
        </p>

        <?php
    }

    /**
     * Show row meta on the plugin screen.
     *
     * @param   mixed $links Plugin Row Meta.
     * @param   mixed $file Plugin Base file.
     * @return  array
     */
    public static function plugin_row_meta($links, $file)
    {
        if (plugin_basename(GFEXCEL_PLUGIN_FILE) !== $file) {
            return $links;
        }
        return array_merge($links, [
            'donate' => '<a href="' . esc_url('https://www.paypal.me/doekenorg') . '" aria-label="' . esc_attr__('Make a donation', GFExcel::$slug) . '">' . esc_html__('Make a donation', GFExcel::$slug) . '</a>',
        ]);
    }

    /**
     * Add settings link to plugin page
     * @param $links
     * @param $file
     * @return array
     */
    public static function plugin_action_links($links, $file)
    {
        if (plugin_basename(GFEXCEL_PLUGIN_FILE) !== $file) {
            return $links;
        }
        return array_merge([
            'settings' => '<a href="' . admin_url('admin.php?page=gf_settings&subview=gf-entries-in-excel') . '" aria-label="' . esc_attr__('View settings', GFExcel::$slug) . '">' . esc_html__('Settings', GFExcel::$slug) . '</a>',
        ], $links);
    }

    public static function gform_form_actions($form_actions, $form_id)
    {
        $form_actions['download'] = array(
            'label' => __('Download', GFExcel::$slug),
            'title' => __('Download entries in Excel', GFExcel::$slug),
            'url' => GFExcel::url($form_id),
            'menu_class' => 'download',
        );

        return $form_actions;
    }

    public function form_settings($form)
    {
        //reads current form settings
        $settings = $this->get_form_settings($form);
        $this->set_settings($settings);

        if ($this->is_save_postback()) {
            $this->saveSettings($form);
            $form = GFFormsModel::get_form_meta($form['id']);
        }

        if ($this->is_postback()) {
            if (!rgempty('regenerate_hash')) {
                $form = GFExcel::setHash($form['id']);
                GFCommon::add_message(__('The download url has been regenerated.', GFExcel::$slug), false);
            }
        }

        GFCommon::display_admin_message();
        printf(
            '<h3>%s</h3>',
            esc_html__(GFExcel::$name, GFExcel::$slug)
        );

        printf('<h4 class="gaddon-section-title gf_settings_subgroup_title">%s:</h4>',
            esc_html__('Download url', GFExcel::$slug)
        );

        $url = GFExcel::url($form['id']);

        echo "<form method=\"post\">";
        printf(
            "<p>
                <input style='width:80%%;' type='text' value='%s' readonly />&nbsp;<input 
                onclick=\"%s\"
                class='button' type='submit' name='regenerate_hash' 
                value='" . __('Regenerate url', GFExcel::$slug) . "'/> 
            </p>",
            $url,
            "return confirm('" . __('This changes the download url permanently!', GFExcel::$slug) . "');"
        );
        echo "</form>";

        echo "<form method=\"post\" action=\"" . $url . "\" target=\"_blank\">
        <h4>" . esc_html__('Select (optional) Date Range', GFExcel::$slug) . " " .
            gform_tooltip('export_date_range', '', true) . "</h4>" .
            "<div class='download-block'>
            <div class=\"date-field\">
                <input type=\"text\" id=\"start_date\" name=\"start_date\" style=\"width:90%\" />
                <label for=\"start_date\">" . esc_html__('Start', 'gravityforms') . "</label>
            </div>

            <div class=\"date-field\">
                <input type=\"text\" id=\"end_date\" name=\"end_date\" style=\"width:90%\" />
                <label for=\"end_date\">" . esc_html__('End', 'gravityforms') . "</label>
            </div>
            
            <div class=\"download-button\">
                <button class='button-primary'>" . esc_html__('Download', GFExcel::$slug) . "</button> " .
            sprintf("%s: <strong>%d</strong>",
                __('Download count', GFExcel::$slug),
                $this->download_count($form)
            ) . "
            <a class='button' href='?" . $_SERVER['QUERY_STRING'] . "&gf_action=" . CountDownloads::ACTION_RESET . "'>" .
            esc_html__('Reset count', GFExcel::$slug) .
            "</a>
            </div></div>
        </form>";

        echo "<br/>";

        echo "<form method=\"post\">";
        $this->generalSettings($form);

        $this->sortableFields($form);

        $this->settings_save(['value' => __("Save settings", GFExcel::$slug)]);
        echo "</form>";
    }

    /**
     * Handles the download of multiple forms as a bulk action.
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function handle_bulk_actions()
    {
        if (!current_user_can('editor') &&
            !current_user_can('administrator') &&
            !GFCommon::current_user_can_any('gravityforms_export_entries')) {
            return false; // How you doin?
        }

        if ($this->current_action() === self::BULK_DOWNLOAD && array_key_exists('form', $_REQUEST)) {
            $form_ids = (array) $_REQUEST['form'];
            if (count($form_ids) < 1) {
                return false;
            }
            $renderer = count($form_ids) > 1
                ? new PHPExcelMultisheetRenderer()
                : new PHPExcelRenderer();

            foreach ($form_ids as $form_id) {
                $output = new GFExcelOutput((int) $form_id, $renderer);
                $output->render();
            }

            $renderer->renderOutput();
            return true;
        }

        return false; // i'm DONE!
    }

    /**
     * Add GFExcel download option to bulk actions dropdown
     * @param $actions
     * @return array
     */
    public function bulk_actions($actions)
    {
        $actions[self::BULK_DOWNLOAD] = esc_html__('Download as one Excel file', GFExcel::$slug);
        return $actions;
    }

    private function current_action()
    {
        if (isset($_REQUEST['filter_action']) && !empty($_REQUEST['filter_action'])) {
            return false;
        }

        if (isset($_REQUEST['action']) && -1 != $_REQUEST['action']) {
            return $_REQUEST['action'];
        }

        if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2']) {
            return $_REQUEST['action2'];
        }

        return false;
    }

    /**
     * Returns the number of downloads
     * @param $form
     * @return int
     */
    private function download_count($form)
    {
        if (array_key_exists("gfexcel_download_count", $form)) {
            return (int) $form["gfexcel_download_count"];
        }

        return 0;
    }

    private function select_sort_field_options($form)
    {
        $fields = array_merge([
            [
                'value' => 'date_created',
                'label' => __("Date of entry", GFExcel::$slug),
            ]
        ], array_map(function ($field) {
            return [
                'value' => $field->id,
                'label' => $field->label,
            ];
        }, (array) $form['fields']));

        $this->settings_select([
            'name' => 'gfexcel_output_sort_field',
            'choices' => $fields,
            'default_value' => $this->repository->getSortField(),
        ]);
    }

    private function select_order_options()
    {
        $this->settings_select([
            'name' => 'gfexcel_output_sort_order',
            'choices' => [[
                'value' => 'ASC',
                'label' => __("Acending", GFExcel::$slug)
            ], [
                'value' => 'DESC',
                'label' => __("Descending", GFExcel::$slug)
            ]],
            'default_value' => $this->repository->getSortOrder(),
        ]);
    }

    private function saveSettings($form)
    {
        /** php5.3 proof. */
        $gfexcel_keys = array_filter(array_keys($_POST), function ($key) {
            return stripos($key, 'gfexcel_') === 0;
        });

        $form_meta = GFFormsModel::get_form_meta($form['id']);

        foreach ($gfexcel_keys as $key) {
            $form_meta[$key] = $_POST[$key];
        }

        foreach ($this->get_posted_settings() as $key => $value) {
            if ($key === FieldsRepository::KEY_DISABLED_FIELDS) {
                if (is_array($value)) {
                    $value = implode(',', array_keys(array_filter($value)));
                }
            }
            if ($key === GFExcel::KEY_CUSTOM_FILENAME) {
                $value = preg_replace('/\.(xlsx|csv)$/is', '', $value);
                $value = preg_replace('/[^a-z0-9_-]+/is', '_', $value);
            }
            $form_meta[$key] = $value;
        }

        GFFormsModel::update_form_meta($form['id'], $form_meta);
        GFCommon::add_message(__('The settings have been saved.', GFExcel::$slug), false);
    }


    /**
     * Remove filename so it returns the newly formatted filename
     *
     * @return array
     */
    public function get_current_settings()
    {
        $settings = parent::get_current_settings();
        unset($settings[GFExcel::KEY_CUSTOM_FILENAME]);

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function get_form_settings($form)
    {
        $settings = array_filter((array) parent::get_form_settings($form));
        return array_merge($settings, array_reduce(array_keys($form), function ($settings, $key) use ($form) {
            if (strpos($key, 'gfexcel_') === 0) {
                $settings[$key] = $form[$key];
            }
            return $settings;
        }, []));
    }

    private function generalSettings($form)
    {
        $this->settings(apply_filters('gfexcel_general_settings', [[
            'title' => __('General settings', GFExcel::$slug),
            'fields' => [
                [
                    'name' => 'enable_notes',
                    'label' => __('Enable notes', GFExcel::$slug),
                    'type' => 'checkbox',
                    'choices' => [[
                        'name' => GFExcel::KEY_ENABLED_NOTES,
                        'label' => __('Yes, enable the notes for every entry', GFExcel::$slug),
                        'value' => '1',
                        'default_value' => $this->enabled_notes($form),
                    ]],
                ],
                [
                    'name' => 'order_by',
                    'type' => 'callback',
                    'label' => __("Order by", GFExcel::$slug),
                    'callback' => function () use ($form) {
                        $this->select_sort_field_options($form);
                        echo ' ';
                        $this->select_order_options();
                    }
                ],
                [
                    'name' => GFExcelConfigConstants::GFEXCEL_RENDERER_TRANSPOSE,
                    'type' => 'radio',
                    'label' => __('Columns position', GFExcel::$slug),
                    'default_value' => @$form[GFExcelConfigConstants::GFEXCEL_RENDERER_TRANSPOSE] ?: 0,
                    'choices' => [
                        [
                            'name' => GFExcelConfigConstants::GFEXCEL_RENDERER_TRANSPOSE,
                            'label' => __('At the top (normal)', GFExcel::$slug),
                            'value' => 0,
                        ],
                        [
                            'name' => GFExcelConfigConstants::GFEXCEL_RENDERER_TRANSPOSE,
                            'label' => __('At the left (transposed)', GFExcel::$slug),
                            'value' => 1,
                        ]
                    ]
                ],
                [
                    'label' => __('Custom filename', GFExcel::$slug),
                    'type' => 'text',
                    'name' => GFExcel::KEY_CUSTOM_FILENAME,
                    'description' => __('Only letters, numbers and dashes are allowed. The rest will be stripped. Leave empty for default.', GFExcel::$slug)
                ],
                [
                    'label' => __('File extension', GFExcel::$slug),
                    'type' => 'select',
                    'name' => GFExcel::KEY_FILE_EXTENSION,
                    'default_value' => @$form[GFExcel::KEY_FILE_EXTENSION],
                    'choices' => array_map(function ($extension) {
                        return
                            [
                                'name' => GFExcel::KEY_FILE_EXTENSION,
                                'label' => '.' . $extension,
                                'value' => $extension,
                            ];
                    }, ['xlsx', 'csv',]),
                ],
                [
                    'label' => __('Attach single entry to notification', GFExcel::$slug),
                    'type' => 'select',
                    'name' => GFExcel::KEY_ATTACHMENT_NOTIFICATION,
                    'default_value' => @$form[GFExcel::KEY_ATTACHMENT_NOTIFICATION],
                    'choices' => $this->getNotifications(),
                ],
            ],
        ]]));
    }

    /**
     * Adds the sortable fields section to the settings page
     * @param $form
     */
    private function sortableFields($form)
    {
        $repository = new FieldsRepository($form);
        $disabled_fields = $repository->getDisabledFields();
        $all_fields = $repository->getFields($unfiltered = true);

        $active_fields = $inactive_fields = [];
        foreach ($all_fields as $field) {
            $array_name = in_array($field->id, $disabled_fields) ? 'inactive_fields' : 'active_fields';
            array_push($$array_name, $field);
        }
        $active_fields = $repository->sortFields($active_fields);

        $this->single_section([
            'title' => __('Field settings', GFExcel::$slug),
            'class' => 'sortfields',
            'fields' => [
                [
                    'label' => __('Disabled fields', GFExcel::$slug),
                    'name' => 'gfexcel_disabled_fields',
                    'move_to' => 'gfexcel_enabled_fields',
                    'type' => 'sortable',
                    'class' => 'fields-select',
                    'side' => 'left',
                    'value' => @$form['gfexcel_disabled_fields'] ?: '',
                    'choices' => array_map(function (\GF_Field $field) {
                        return [
                            'value' => $field->id,
                            'label' => $field->label,
                        ];
                    }, $inactive_fields),
                ], [
                    'label' => __('Enable & sort the fields', GFExcel::$slug),
                    'name' => 'gfexcel_enabled_fields',
                    'value' => @$form['gfexcel_enabled_fields'] ?: '',
                    'move_to' => 'gfexcel_disabled_fields',
                    'type' => 'sortable',
                    'class' => 'fields-select',
                    'side' => 'right',
                    'choices' => array_map(function (\GF_Field $field) {
                        return [
                            'value' => $field->id,
                            'label' => $field->label,
                        ];
                    }, $active_fields),
                ],
            ],
        ]);
    }

    /**
     * Renders the html for a single sortable fields.
     * I don't like this inline html approach Gravity Forms uses.
     * @param $field
     * @param bool $echo
     * @return string
     */
    public function settings_sortable($field, $echo = true)
    {
        $attributes = $this->get_field_attributes($field);
        $name = '' . esc_attr($field['name']);
        $value = rgar($field, 'value'); //comma-separated list from database

        // If no choices were provided and there is a no choices message, display it.
        if ((empty($field['choices']) || !rgar($field, 'choices')) && rgar($field, 'no_choices')) {
            $html = $field['no_choices'];
        } else {

            $html = sprintf('<input type="hidden" name="%s" value="%s">', '_gaddon_setting_' . $name, $value);
            $html .= sprintf(
                '<ul id="%1$s" %2$s data-send-to="%4$s">%3$s</ul>',
                $name, implode(' ', $attributes), implode("\n", array_map(function ($choice) use ($field) {
                return sprintf(
                    '<li data-value="%s">
                        <div class="field"><i class="fa fa-bars"></i> %s</div>
                        <div class="move">
                            <i class="fa fa-arrow-right"></i>
                            <i class="fa fa-close"></i>
                        </div>
                    </li>',
                    $choice['value'], $choice['label']
                );
            }, $field['choices'])), $field['move_to']);

            $html .= rgar($field, 'after_select');
        }

        if ($this->field_failed_validation($field)) {
            $html .= $this->get_error_icon($field);
        }

        if ($echo) {
            echo $html;
        }

        return $html;
    }

    /**
     * Renders the html for the sortable fields
     * @param $field
     */
    public function single_setting_row_sortable($field)
    {
        $display = rgar($field, 'hidden') || rgar($field, 'type') == 'hidden' ? 'style="display:none;"' : '';

        // Prepare setting description.
        $description = rgar($field, 'description') ? '<span class="gf_settings_description">' . $field['description'] . '</span>' : null;

        if (array_key_exists('side', $field) && $field['side'] === "left") {
            ?>
            <tr id="gaddon-setting-row-<?php echo $field['name'] ?>" <?php echo $display; ?>>
        <?php } ?>
        <td style="vertical-align: top; ">
            <p><strong><?php $this->single_setting_label($field); ?></strong></p>
            <?php
            $this->single_setting($field);
            echo $description;
            ?>
        </td>
        <?php if (array_key_exists('side', $field) && $field['side'] === "right") { ?></tr><?php }
    }

    /**
     * Adds javascript for the sortable to the page
     * @param array $ids
     * @param string $connector_class
     */
    private function sortable_script(array $ids, $connector_class = 'connected-sortable')
    {
        $ids = implode(', ', array_map(function ($id) {
            return '#' . $id;
        }, $ids));

        wp_add_inline_script('gfexcel-js', '(function($) { $(document).ready(function() { gfexcel_sortable(\'' . $ids . '\',\'' . $connector_class . '\'); }); })(jQuery);');
    }

    /**
     * Add javascript and custom css to the page
     */
    public function register_assets()
    {
        $this->sortable_script(['gfexcel_enabled_fields', 'gfexcel_disabled_fields'], 'fields-select');
    }

    /**
     * Get the assets path
     * @return string
     */
    public static function assets()
    {
        return plugin_dir_url(dirname(__DIR__) . '/gfexcel.php');
    }

    public function scripts()
    {
        return array_merge(parent::scripts(), [
            [
                'handle' => 'jquery-ui-sortable',
                'enqueue' => [[
                    'admin_page' => 'form_settings',
                    'tab' => GFExcel::$slug,
                ]],
            ],
            [
                'handle' => 'gfexcel-js',
                'src' => self::assets() . 'public/js/gfexcel.js',
                'enqueue' => [[
                    'admin_page' => 'form_settings',
                    'tab' => GFExcel::$slug,
                ]],
                'deps' => ['jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'],
            ],
        ]);
    }

    public function styles()
    {
        return array_merge(parent::styles(), [
            [
                'handle' => 'gfexcel-css',
                'src' => self::assets() . 'public/css/gfexcel.css',
                'enqueue' => [
                    [
                        'admin_page' => 'form_settings',
                        'tab' => GFExcel::$slug,
                    ],
                    [
                        'admin_page' => 'plugin_settings',
                        'tab' => GFExcel::$slug,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param $notification
     * @param $form
     * @param $entry
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function handle_notification($notification, $form, $entry)
    {
        if (!$this->repository) {
            $this->repository = new FormsRepository($form['id']);
        }

        // get notification to add to by form setting
        if (!$this->repository || $this->repository->getSelectedNotification() !== rgar($notification, 'id')) {
            //Not the right notification
            return $notification;
        }

        // create a file based on the settings in the form, with only this entry.
        $output = new GFExcelOutput($form['id'], new PHPExcelRenderer());
        $output->setEntries([$entry]);

        // save the file to a temporary file
        $this->_file = $output->render($save = true);
        if (!file_exists($this->_file)) {
            return $notification;
        }
        // attach file to $notification['attachments'][]
        $notification['attachments'][] = $this->_file;

        return $notification;
    }


    public static function get_instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new GFExcelAdmin();
        }

        return self::$_instance;
    }

    private function getNotifications()
    {
        $options = [['label' => __('Select a notification', GFExcel::$slug), 'value' => '']];
        foreach ($this->repository->getNotifications() as $key => $notification) {
            $options[] = ['label' => rgar($notification, 'name', __('Unknown')), 'value' => $key];
        }

        return $options;
    }

    public function remove_temporary_file()
    {
        $args = func_get_args();
        $attachments = $args[5];
        if (is_array($attachments) && count($attachments) < 1) {
            return false;
        }
        if (in_array($this->_file, $attachments) && file_exists($this->_file)) {
            unlink($this->_file);
        }
        return true;
    }

    private function plugin_settings_description()
    {
        $html = "<p>";
        $html .= esc_html__('These are global settings for new forms. You can overwrite them per form using the available hooks.', GFExcel::$slug);
        $html .= "</p>";

        return $html;
    }

    private function meta_fields()
    {
        $repository = new FieldsRepository(['fields' => []]);

        //suppress notices of missing form. There is no form, we just need the meta data
        $fields = @$repository->getFields(true);

        return array_map(function ($field) {
            return [
                'label' => $field->label,
                'name' => 'enabled_metafield_' . $field->id,
                'default_value' => true,
            ];
        }, $fields);
    }

    private function enabled_notes($form = [])
    {
        if (array_key_exists(GFExcel::KEY_ENABLED_NOTES, $form)) {
            return (int) $form[GFExcel::KEY_ENABLED_NOTES];
        };

        return $this->get_plugin_setting('notes_enabled');
    }

    /**
     * Get the current usage count from the plugin repo.
     * Info is cached for a week.
     * @return string
     */
    private function getUsageCount()
    {
        if (!$active_installs = get_transient(GFExcel::$slug . '-active_installs')) {
            if (!function_exists('plugins_api')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
            }
            $data = plugins_api('plugin_information', [
                'slug' => GFExcel::$slug,
                'fields' => ['active_installs' => true],
            ]);

            if ($data instanceof \WP_Error || !is_object($data) || !isset($data->active_installs)) {
                return __('countless', GFExcel::$slug);
            }
            $active_installs = $data->active_installs;
            set_transient(GFExcel::$slug . '-active_installs', $active_installs, (60 * 60 * 24 * 7));
        }

        return $active_installs . '+';
    }

    /**
     * Get a target usage count for the plugin repo.
     * @return string
     */
    private function getUsageTarget()
    {
        $current_count = $this->getUsageCount();
        if ($current_count === __('countless', GFExcel::$slug)) {
            return __('even more', GFExcel::$slug);
        }
        $digit = ((int) substr($current_count, 0, 1) + 1);

        return $digit . substr($current_count, 1);
    }

    /**
     * Register native plugin actions
     * @since 1.6.1
     * @return void
     */
    private function registerActions()
    {
        $actions = [
            CountDownloads::class,
            DownloadUrl::class,
        ];

        foreach ($actions as $action) {
            if (class_exists($action)) {
                new $action;
            }
        }
    }
}
