<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Notices\Entities;

use Piggly\WooPixGateway\Vendor\Piggly\Wordpress\Core;
/**
 * The notice class.
 *
 * @package \Piggly\Wordpress
 * @subpackage \Piggly\Wordpress\Notices
 * @version 1.0.0
 * @since 1.0.0
 * @category Notices
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Notice
{
    /**
     * Notice type.
     *
     * @var string
     * @since 1.0.0
     */
    protected $_type;
    /**
     * Notice body.
     *
     * @var string
     * @since 1.0.0
     */
    protected $_body;
    /**
     * Notice is dismissible?
     *
     * @var bool
     * @since 1.0.0
     */
    protected $_isDismissible;
    /**
     * Notice key at settings.
     *
     * @var string
     * @since 1.0.0
     */
    protected $_key;
    /**
     * Notice HTML id.
     *
     * @var string
     * @since 1.0.0
     */
    protected $_id;
    /**
     * CSS classes to notice.
     *
     * @var array
     * @since 1.0.0
     */
    protected $_classes = [];
    /**
     * HTML attributes to notice.
     * 
     * @var array
     * @since 1.0.0
     */
    protected $_attrs = [];
    /**
     * Condition to display admin notice.
     *
     * @var boolean
     * @since 1.0.0
     */
    protected $_showWhen = \true;
    /**
     * Should remove after seen.
     *
     * @var boolean
     */
    protected $_removeAfterSeen = \false;
    /**
     * Create a new notice.
     *
     * @param string $type
     * @param string $body
     * @since 1.0.0
     * @return void
     */
    public function __construct(string $type, string $body = null)
    {
        $this->setType($type);
        if (!\is_null($body)) {
            $this->setBody($body);
        }
    }
    /**
     * Export this notice to an HTML string.
     *
     * @since 1.0.0
     * @return string
     */
    public function export() : string
    {
        if (\is_null($this->_body) || $this->_showWhen === \false) {
            return '';
        }
        $dismissible = $this->_isDismissible ? 'is-dismissible' : '';
        $out = \sprintf('<div id="%s" class="notice %s %s %s" %s>', $this->_id, $this->_type, $dismissible, \implode(' ', $this->_classes), \implode(' ', $this->_attrs));
        $out .= $this->_body;
        $out .= '</div>';
        if ($this->_removeAfterSeen === \true) {
            $notices = get_transient(Core::getPlugin()->getNotices()) ?? [];
            foreach ($notices as $index => $notice) {
                $notice = \unserialize($notice);
                if ($notice->getKey() === $this->getKey()) {
                    unset($notices[$index]);
                    break;
                }
            }
            set_transient(Core::getPlugin()->getNotices(), $notice, 45);
        }
        return $out;
    }
    /**
     * Get notice type.
     *
     * @since 1.0.0
     * @return string
     */
    public function getType() : ?string
    {
        return $this->_type;
    }
    /**
     * Set notice type.
     * It uses esc_attr().
     *
     * @param string $type Notice type.
     * @since 1.0.0
     * @return self
     */
    public function setType(string $type)
    {
        $this->_type = esc_attr($type);
        return $this;
    }
    /**
     * Get notice body.
     *
     * @since 1.0.0
     * @return string
     */
    public function getBody() : string
    {
        return $this->_body;
    }
    /**
     * Set notice body.
     * It uses wp_kses_post().
     *
     * @param string $body Notice body.
     * @since 1.0.0
     * @return self
     */
    public function setBody(string $body)
    {
        $this->_body = wp_kses_post($body);
        return $this;
    }
    /**
     * Get notice is dismissible?
     *
     * @since 1.0.0
     * @return bool
     */
    public function getIsDismissible() : bool
    {
        return $this->_isDismissible ?? \false;
    }
    /**
     * Set notice is dismissible?
     *
     * @param bool $isDismissible Notice is dismissible?
     * @since 1.0.0
     * @return self
     */
    public function setIsDismissible(bool $isDismissible)
    {
        $this->_isDismissible = $isDismissible;
        return $this;
    }
    /**
     * Get notice HTML id.
     *
     * @since 1.0.0
     * @return string|null
     */
    public function getId() : ?string
    {
        return $this->_id ?? null;
    }
    /**
     * Set notice HTML id.
     * It uses esc_attr().
     *
     * @param string $id Notice HTML id.
     * @since 1.0.0
     * @return self
     */
    public function setId(string $id)
    {
        $this->_id = esc_attr($id);
        return $this;
    }
    /**
     * Get notice key at settings.
     *
     * @since 1.0.0
     * @return string|null
     */
    public function getKey() : ?string
    {
        return $this->_key ?? null;
    }
    /**
     * Set notice key at settings.
     *
     * @param string $key Notice key at settings.
     * @since 1.0.0
     * @return self
     */
    public function setKey(string $key)
    {
        $this->_key = $key;
        return $this;
    }
    /**
     * Get all CSS classes.
     *
     * @since 1.0.0
     * @return array
     */
    public function getClasses() : array
    {
        return $this->_classes;
    }
    /**
     * Add CSS classes to this notice.
     * It uses esc_attr().
     *
     * @param string ...$classes
     * @since 1.0.0
     * @return self
     */
    public function addClasses(...$classes)
    {
        foreach ($classes as $class) {
            $this->_classes[] = esc_attr($class);
        }
        return $this;
    }
    /**
     * Get all HTML attributes.
     *
     * @since 1.0.0
     * @return array
     */
    public function getAttrs() : array
    {
        return $this->_attrs;
    }
    /**
     * Add HTML attributes to this notice.
     * It uses esc_attr().
     *
     * @param string ...$attrs
     * @since 1.0.0
     * @return self
     */
    public function addAttrs(...$attrs)
    {
        foreach ($attrs as $attr) {
            $this->_attrs[] = esc_attr($attr);
        }
        return $this;
    }
    /**
     * Show notice only when condition is
     * TRUE.
     *
     * @param boolean $condition
     * @since 1.0.0
     * @return self
     */
    public function showWhen(bool $condition)
    {
        $this->_showWhen = $condition;
        return $this;
    }
    /**
     * Remove notice when it is seen.
     *
     * @param boolean $removeAfterSeen
     * @since 1.0.0
     * @return self
     */
    public function removeAfterSeen(bool $removeAfterSeen)
    {
        $this->_removeAfterSeen = $removeAfterSeen;
        return $this;
    }
    /**
     * When notice should remove after seen it will
     * be considered as a transient.
     *
     * @since 1.0.0
     * @return boolean
     */
    public function isTransient() : bool
    {
        return $this->_removeAfterSeen === \true;
    }
}
