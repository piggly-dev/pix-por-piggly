<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Tables\RecordTable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\WP;
use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Connector;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core\Scaffold\Initiable;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Entities\AbstractEntity;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\BodyValidator;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Helpers\RequestBodyParser;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Notices\NoticeManager;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\Form;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Fields\SchemaExtractor;
use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Post\Interfaces\PostTypeInterface;
/**
 * Manage the custom post type structure.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Post
 * @version 1.0.7
 * @since 1.0.7
 * @category Post
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2022 Piggly Lab <dev@piggly.com.br>
 */
abstract class CustomPostType extends Initiable implements PostTypeInterface
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
     * @since 1.0.8
     * @var string
     */
    protected string $js_version = '0.2.0';
    /**
     * File for template of content page
     * in templates folder of plugin.
     *
     * @since 1.0.8
     * @var string
     */
    protected string $_table_page = 'admin/post-types-table.php';
    /**
     * File for template of content page
     * in templates folder of plugin.
     *
     * @since 1.0.8
     * @var string
     */
    protected string $_content_page = 'admin/post-types-content.php';
    /**
     * Run startup method to class create
     * it own instance.
     *
     * @since 1.0.7
     * @return void
     */
    public function startup()
    {
        WP::add_action('admin_menu', $this, 'add_menu', 99);
    }
    /**
     * Create a new menu at Wordpress admin menu bar.
     *
     * @since 1.0.7
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
     * @since 1.0.7
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_media();
        \wp_enqueue_script(\sprintf('pgly-wps-settings-%s-js', $this->js_version), Connector::plugin()->getUrl() . '/assets/vendor/js/pgly-wps-settings.js', null, Connector::plugin()->getVersion(), \true);
        \wp_enqueue_style(\sprintf('pgly-wps-settings-%s-css', $this->js_version), Connector::plugin()->getUrl() . '/assets/vendor/css/pgly-wps-settings.css', null, Connector::plugin()->getVersion(), 'all');
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
            $this->action();
            $this->post_load();
        } catch (Exception $e) {
            echo '<div class="notice notice-error is-dismissible"><p>' . $e->getMessage() . '</p></div>';
            if ($e->getCode(404)) {
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
     * @since 1.0.7
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
     * @since 1.0.7
     * @return void
     */
    protected function prepare_fields()
    {
        // Try to load fields
        if (!empty($this->query_id)) {
            $fields = static::entityModel()::getRepo()::byId($this->query_id, 'OBJECT');
            if (empty($fields)) {
                throw new Exception(\sprintf('O %s não foi localizado, tente novamente mais tarde ou selecione outro %s.', static::singularName(), static::singularName()), 404);
            }
            $this->entity = static::entityModel()::fromRecord($fields);
        }
    }
    /**
     * Process action.
     *
     * @since 1.0.7
     * @return void
     */
    protected function action()
    {
        switch ($this->query_action) {
            case 'edit':
            case 'add':
                return $this->edit();
            case 'remove':
                return $this->remove();
        }
    }
    /**
     * Load any data required before show fields.
     *
     * @since 1.0.7
     * @return void
     */
    protected function post_load()
    {
    }
    /**
     * Get fields from post request.
     *
     * @param RequestBodyParser $requestBody
     * @since 1.0.7
     * @return void
     */
    protected function get_fields(RequestBodyParser $requestBody)
    {
        if (!$requestBody->isPOST()) {
            return;
        }
        $prefix = $this->fieldPrefix();
        $body = $requestBody->body();
        $parsed = BodyValidator::validate($body, SchemaExtractor::extract($this->form()->fields()), $prefix);
        /* Verify the nonce before proceeding. */
        if (empty($body[$prefix . 'nonce']) || !\wp_verify_nonce($body[$prefix . 'nonce'], $prefix . 'save')) {
            throw new Exception('O nonce para o envio do formulário é inválido.');
        }
        $this->entity = static::entityModel()::fromBody($parsed);
    }
    /**
     * Edit record.
     *
     * @since 1.0.7
     * @return void
     */
    protected function edit() : void
    {
        $requestBody = new RequestBodyParser();
        if (!$requestBody->isPOST()) {
            return;
        }
        $this->get_fields($requestBody);
        $this->entity->save();
        NoticeManager::echoNotice(\sprintf('%s salvo com sucesso. Você será redirecionado em instantes.', static::singularName()));
        $this->redirectTo($this->entity->id());
    }
    /**
     * Remove record.
     *
     * @since 1.0.7
     * @return void
     */
    protected function remove() : void
    {
        if (empty($this->query_id)) {
            throw new Exception(\sprintf('O ID do %s não pode ser vazio.', \strtolower(static::singularName())));
        }
        static::entityModel()::getRepo()::delete([static::entityModel()::primaryKey() => $this->query_id]);
        NoticeManager::echoNotice(\sprintf('%s removido com sucesso. Você será redirecionado em instantes.', static::singularName()));
        $this->redirectToTable();
    }
    /**
     * Redirect to record by id.
     *
     * @param mixed $id
     * @since 1.0.7
     * @return void
     */
    protected function redirectTo($id)
    {
        $url = \add_query_arg(['id' => $id, 'action' => 'edit'], \admin_url('admin.php?page=' . static::getSlug() . '-content'));
        ?>
<script lang="javascript">
	setTimeout(function() {
		window.location.href = "<?php 
        echo $url;
        ?>";
	}, 3000);
</script>
<?php 
    }
    /**
     * Redirect to table.
     *
     * @since 1.0.7
     * @return void
     */
    protected function redirectToTable()
    {
        $url = \admin_url('admin.php?page=' . static::getSlug());
        ?>
<script lang="javascript">
	setTimeout(function() {
		window.location.href = "<?php 
        echo $url;
        ?>";
	}, 3000);
</script>
<?php 
    }
}
