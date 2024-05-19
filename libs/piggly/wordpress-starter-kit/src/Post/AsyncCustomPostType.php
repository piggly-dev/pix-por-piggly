<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Repository\WPRepository;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Tables\RecordTable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Connector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\JSONable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Entities\AbstractEntity;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\RequestBodyParser;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Form;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\SchemaExtractor;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Interfaces\PostTypeInterface;
/**
 * Manage the custom post type structure.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Post
 * @version 1.0.9
 * @since 1.0.9
 * @category Post
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
abstract class AsyncCustomPostType extends JSONable implements PostTypeInterface
{
    /**
     * ID from query string variable.
     *
     * @var integer|null
     */
    protected ?int $query_id = null;
    /**
     * Action from query string variable.
     *
     * @var integer|null
     */
    protected string $query_action = 'add';
    /**
     * Current entity.
     *
     * @since 1.0.10
     * @var AbstractEntity
     */
    protected $entity;
    /**
     * Version for PGLY WPS SETTINGS lib.
     *
     * @since 1.0.9
     * @var string
     */
    protected string $js_version = '0.2.0';
    /**
     * File for template of content page
     * in templates folder of plugin.
     *
     * @since 1.0.9
     * @var string
     */
    protected string $_table_page = 'admin/post-types-table.php';
    /**
     * File for template of content page
     * in templates folder of plugin.
     *
     * @since 1.0.9
     * @var string
     */
    protected string $_content_page = 'admin/post-types-content.php';
    /**
     * Run startup method to class create
     * it own instance.
     *
     * @since 1.0.9
     * @return void
     */
    public function startup()
    {
        WP::add_action('admin_menu', $this, 'add_menu', 99);
        $this->handlers();
    }
    /**
     * Handle all endpoints.
     *
     * @since 1.0.12
     * @return void
     */
    public function handlers()
    {
    }
    /**
     * Create a new menu at Wordpress admin menu bar.
     *
     * @since 1.0.9
     * @return void
     */
    public function add_menu()
    {
        $slug = static::getSlug();
        add_menu_page('Visualizar ' . static::pluralName(), static::pluralName(), 'edit_posts', $slug, [$this, 'table_page'], static::getIcon());
        add_submenu_page($slug, 'Visualizar ' . static::pluralName(), 'Visualizar ' . static::pluralName(), 'edit_posts', $slug, '', 1);
        add_submenu_page($slug, 'Adicionar ' . static::singularName(), 'Adicionar ' . static::singularName(), 'edit_posts', $slug . '-content', [$this, 'content_page'], 10);
    }
    /**
     * Enqueue scripts and styles.
     *
     * @since 1.0.9
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_media();
        $name = \sprintf('pgly-wps-settings-%s', $this->js_version);
        \wp_enqueue_script('axios', 'https://unpkg.com/axios/dist/axios.min.js', null, '0.27.2', \true);
        \wp_enqueue_script($name, Connector::plugin()->getUrl() . 'assets/vendor/js/pgly-wps-settings.js', ['axios'], $this->js_version, \true);
        \wp_enqueue_style($name, Connector::plugin()->getUrl() . 'assets/vendor/css/pgly-wps-settings.min.css', null, $this->js_version, 'all');
        \wp_localize_script($name, Connector::plugin()->getName(), ['ajax_url' => admin_url('admin-ajax.php'), 'x_security' => \wp_create_nonce(static::nonceAction()), 'plugin_url' => admin_url('admin.php?page=' . static::getSlug()), 'assets_url' => Connector::plugin()->getUrl()]);
    }
    /**
     * Load page to view table listing.
     *
     * @since 2.0.0
     * @return void
     */
    public function table_page()
    {
        $this->enqueue_scripts();
        echo '<div id="pgly-wps-plugin" class="pgly-wps--settings">';
        require_once Connector::plugin()->getTemplatePath() . $this->_table_page;
        echo '</div>';
    }
    /**
     * Load page to view table listing.
     *
     * @since 2.0.0
     * @return void
     */
    public function content_page()
    {
        $this->enqueue_scripts();
        try {
            $this->fill_query();
            $this->prepare_fields();
            $this->post_load();
        } catch (Exception $e) {
            echo '<div class="notice notice-error is-dismissible"><p>' . $e->getMessage() . '</p></div>';
            if ($e->getCode() === 404) {
                exit;
            }
        }
        echo '<div id="pgly-wps-plugin" class="pgly-wps--settings">';
        require_once Connector::plugin()->getTemplatePath() . $this->_content_page;
        echo '</div>';
    }
    /**
     * Fill query values from query string data.
     *
     * @since 1.0.9
     * @return void
     */
    protected function fill_query()
    {
        $id = \filter_input(\INPUT_GET, 'id', \FILTER_SANITIZE_NUMBER_INT, \FILTER_NULL_ON_FAILURE);
        $action = \filter_input(\INPUT_GET, 'action', \FILTER_SANITIZE_STRING, \FILTER_NULL_ON_FAILURE);
        $this->query_id = !empty($id) ? \intval($id) : null;
        $this->query_action = empty($action) ? 'add' : $action;
        // Validate action
        if (!\in_array($this->query_action, ['edit', 'remove', 'add'])) {
            throw new Exception('Ação indisponível.', 404);
        }
    }
    /**
     * Prepare fields to editing.
     *
     * @param array $options
     * @since 1.0.9
     * @return void
     */
    protected function prepare_fields(array $options = [])
    {
        $this->entity = static::entityModel($options);
        // Try to load fields
        if (!empty($this->query_id)) {
            $fields = static::entityModel()::getRepo()::byId($this->query_id, 'OBJECT');
            if (empty($fields)) {
                throw new Exception('O registro não foi localizado, tente novamente mais tarde ou selecione outro registro.', 404);
            }
            $this->entity = static::entityModel()::fromRecord($fields);
        }
    }
    /**
     * Load any data required before show fields.
     *
     * @since 1.0.9
     * @return void
     */
    protected function post_load()
    {
    }
    /**
     * Get fields from post request.
     *
     * @param RequestBodyParser $requestBody
     * @since 1.0.10
     * @return void
     */
    protected function get_fields(RequestBodyParser $requestBody)
    {
        if (!$requestBody->isPOST()) {
            return;
        }
        $this->entity = static::entityModel()::fromBody($this->parse($requestBody, SchemaExtractor::extract($this->form()->fields()), ['nonce_name' => 'x_security', 'nonce_action' => static::nonceAction()]), $requestBody->body());
    }
    /**
     * Edit record.
     *
     * @since 1.0.9
     * @throws Exception
     */
    protected function edit() : void
    {
        $requestBody = new RequestBodyParser();
        if (!$requestBody->isPOST()) {
            throw new Exception('Método HTTP não disponível.', 405);
        }
        $this->get_fields($requestBody);
        $this->entity->save();
    }
    /**
     * Remove record.
     *
     * @since 1.0.9
     * @return bool
     * @throws Exception
     */
    protected function remove() : bool
    {
        $requestBody = new RequestBodyParser();
        if (!$requestBody->isPOST()) {
            throw new Exception('Método HTTP não disponível.', 405);
        }
        $body = $requestBody->body();
        $this->authorizationCheck($body);
        $id = $body['id'] ?? null;
        if (empty($id)) {
            throw new Exception('O ID é requerido.', 422);
        }
        return static::entityModel()::getRepo()::delete([static::entityModel()::primaryKey() => $id]);
    }
}
