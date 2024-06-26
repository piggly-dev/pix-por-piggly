<?php

namespace Piggly\WooPixGateway\Vendor\Piggly\Pix\Api\Payloads;

use Exception;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Api\Payloads\Entities\Amount;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Api\Payloads\Entities\Calendar;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Api\Payloads\Concerns\UseExtra;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Api\Payloads\Entities\Location;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Api\Payloads\Entities\Person;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Api\Payloads\Entities\Pix;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Exceptions\CannotParseKeyTypeException;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Exceptions\InvalidFieldException;
use Piggly\WooPixGateway\Vendor\Piggly\Pix\Parser;
use RuntimeException;
/**
 * Cob payload modeling.
 *
 * @package \Piggly\Pix
 * @subpackage \Piggly\Pix\Api\Payloads
 * @version 2.0.0
 * @since 2.0.0
 * @category Payload
 * @author Caique Araujo <caique@piggly.com.br>
 * @author Piggly Lab <dev@piggly.com.br>
 * @license MIT
 * @copyright 2021 Piggly Lab <dev@piggly.com.br>
 */
class Cob
{
    use UseExtra;
    /**
     * Cob status as "NAO_SETADO" .
     *
     * @var string
     * @since 2.0.0
     */
    const STATUS_UNSET = 'NAO_SETADO';
    /**
     * Cob status as "ATIVA" .
     *
     * @var string
     * @since 2.0.0
     */
    const STATUS_ACTIVE = 'ATIVA';
    /**
     * Cob status as "CONCLUIDA" .
     *
     * @var string
     * @since 2.0.0
     */
    const STATUS_FINISHED = 'CONCLUIDA';
    /**
     * Cob status as "REMOVIDA_PELO_USUARIO_RECEBEDOR" .
     *
     * @var string
     * @since 2.0.0
     */
    const STATUS_REMOVED_BY_RECEPTOR = 'REMOVIDA_PELO_USUARIO_RECEBEDOR';
    /**
     * Cob status as "REMOVIDA_PELO_PSP" .
     *
     * @var string
     * @since 2.0.0
     */
    const STATUS_REMOVED_BY_PSP = 'REMOVIDA_PELO_PSP';
    /**
     * All cob status available.
     *
     * @var array<string>
     * @since 2.0.0
     */
    const STATUSES = [self::STATUS_UNSET, self::STATUS_ACTIVE, self::STATUS_FINISHED, self::STATUS_REMOVED_BY_RECEPTOR, self::STATUS_REMOVED_BY_PSP];
    /**
     * Cob type as "COB_IMMEDIATE".
     *
     * @var string
     * @since 2.0.0
     */
    const TYPE_IMMEDIATE = 'COB_IMMEDIATE';
    /**
     * Cob type as "COB_DUE".
     *
     * @var string
     * @since 2.0.0
     */
    const TYPE_DUE = 'COB_DUE';
    /**
     * All cob types available.
     *
     * @var array<string>
     * @since 2.0.0
     */
    const TYPES = [self::TYPE_IMMEDIATE, self::TYPE_DUE];
    /**
     * Receiver person.
     *
     * @since 2.0.0
     * @var Person|null
     */
    protected $receiver = null;
    /**
     * Debtor person.
     *
     * @since 2.0.0
     * @var Person|null
     */
    protected $debtor = null;
    /**
     * Calendar rules.
     *
     * @since 2.0.0
     * @var Calendar|null
     */
    protected $calendar = null;
    /**
     * Amount rules.
     * @since 2.0.0
     * @var Amount|null
     */
    protected $amount = null;
    /**
     * Location data.
     *
     * @since 2.0.0
     * @var Location|null
     */
    protected $location = null;
    /**
     * Pix data.
     *
     * @since 2.0.0
     * @var Pix|null
     */
    protected $pix = null;
    /**
     * Collection of pix related to cob.
     *
     * @since 3.0.0
     * @var array<Pix>
     */
    protected $pixes = [];
    /**
     * Pix key.
     * @since 2.0.0
     * @var string|null
     */
    protected $pixKey = null;
    /**
     * Pix key.
     * @since 2.0.0
     * @var string|null
     */
    protected $pixCopyAndPast = null;
    /**
     * Pix key type.
     * @since 2.0.0
     * @var string|null
     */
    protected $pixKeyType = null;
    /**
     * Transaction id.
     * @since 2.0.0
     * @var string|null
     */
    protected $tid = null;
    /**
     * Request message to debtor.
     *
     * @since 2.0.0
     * @var string|null
     */
    protected $requestToDebtor = null;
    /**
     * Cob revision.
     *
     * @since 2.0.0
     * @var int|null
     */
    protected $revision = null;
    /**
     * Cob status.
     *
     * @since 2.0.0
     * @var string|null
     */
    protected $status = null;
    /**
     * Set the cob receiver.
     * Will change $person type to Person::TYPE_RECEIVER.
     *
     * @param Person $person
     * @since 2.0.0
     * @since 3.0.0 Clone person to avoid changes.
     * @return self
     */
    public function setReceiver(Person $person)
    {
        $new_person = new Person(Person::TYPE_RECEIVER, $person->getName(), $person->getDocument());
        $this->receiver = $new_person->import($person->export());
        return $this;
    }
    /**
     * Set the cob debtor.
     * Will change $person type to Person::TYPE_DEBTOR.
     *
     * @param Person $person
     * @since 2.0.0
     * @since 3.0.0 Clone person to avoid changes.
     * @return self
     */
    public function setDebtor(Person $person)
    {
        $new_person = new Person(Person::TYPE_DEBTOR, $person->getName(), $person->getDocument());
        $this->debtor = $new_person->import($person->export());
        return $this;
    }
    /**
     * Set the cob calendar rules.
     *
     * @param Calendar $calendar
     * @since 2.0.0
     * @return self
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
        return $this;
    }
    /**
     * Set the cob amount rules.
     *
     * @param Amount $amount
     * @since 2.0.0
     * @return self
     */
    public function setAmount(Amount $amount)
    {
        $this->amount = $amount;
        return $this;
    }
    /**
     * Set the cob location data.
     *
     * @param Location $location
     * @since 2.0.0
     * @return self
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
        return $this;
    }
    /**
     * Set the cob pix data.
     *
     * @param Pix $pix
     * @since 2.0.0
     * @return self
     */
    public function setPix(Pix $pix)
    {
        $this->pix = $pix;
        \array_unshift($this->pixes, $pix);
    }
    /**
     * Set the cob pix key.
     *
     * @param string $pixKey
     * @since 2.0.0
     * @return self
     * @throws CannotParseKeyTypeException Cannot parse type of pix key, may be invalid.
     */
    public function setPixKey(string $pixKey)
    {
        $this->pixKeyType = Parser::getKeyType($pixKey);
        $this->pixKey = $pixKey;
        return $this;
    }
    /**
     * Set the cob pix copy and past key.
     *
     * @param string $emv
     * @since 3.0.0
     * @return self
     * @throws CannotParseKeyTypeException Cannot parse type of pix key, may be invalid.
     */
    public function setPixCopyPaste(string $emv)
    {
        $this->pixCopyAndPast = $emv;
        return $this;
    }
    /**
     * Set the cob transaction id.
     *
     * @param string $tid
     * @since 2.0.0
     * @return self
     */
    public function setTid(string $tid)
    {
        $this->tid = $tid;
        return $this;
    }
    /**
     * Set the cob request message to debtor.
     *
     * @param string $requestToDebtor
     * @since 2.0.0
     * @return self
     */
    public function setRequestToDebtor(string $requestToDebtor)
    {
        $this->requestToDebtor = $requestToDebtor;
        return $this;
    }
    /**
     * Set the cob revision.
     *
     * @param int $revision
     * @since 2.0.0
     * @return self
     */
    public function setRevision(int $revision)
    {
        $this->revision = $revision;
        return $this;
    }
    /**
     * Set the cob status.
     *
     * @param string $status
     * @since 2.0.0
     * @return self
     * @throws InvalidFieldException
     */
    public function setStatus(string $status)
    {
        try {
            static::validateStatus($status);
        } catch (Exception $e) {
            throw new InvalidFieldException('Cob.Status', $status, $e->getMessage());
        }
        $this->status = $status;
        return $this;
    }
    /**
     * Get receiver to current cob.
     *
     * @since 2.0.0
     * @return Person|null
     */
    public function getReceiver() : ?Person
    {
        return $this->receiver;
    }
    /**
     * Get debtor to current cob.
     *
     * @since 2.0.0
     * @return Person|null
     */
    public function getDebtor() : ?Person
    {
        return $this->debtor;
    }
    /**
     * Get calendar to current cob.
     *
     * @since 2.0.0
     * @return Calendar|null
     */
    public function getCalendar() : ?Calendar
    {
        return $this->calendar;
    }
    /**
     * Get amount to current cob.
     *
     * @since 2.0.0
     * @return Amount|null
     */
    public function getAmount() : ?Amount
    {
        return $this->amount;
    }
    /**
     * Get location to current cob.
     *
     * @since 2.0.0
     * @return Location|null
     */
    public function getLocation() : ?Location
    {
        return $this->location;
    }
    /**
     * Get pix to current cob.
     *
     * @since 2.0.0
     * @return Pix|null
     */
    public function getPix() : ?Pix
    {
        return $this->pix;
    }
    /**
     * Get pix key to current cob.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function getPixKey() : ?string
    {
        return $this->pixKey;
    }
    /**
     * Get pix key to current cob.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function getPixKeyType() : ?string
    {
        return $this->pixKeyType;
    }
    /**
     * Get request message to debtor of current cob.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function getRequestToDebtor() : ?string
    {
        return $this->requestToDebtor;
    }
    /**
     * Get revision to current cob.
     *
     * @since 2.0.0
     * @return int|null
     */
    public function getRevision() : ?int
    {
        return $this->revision;
    }
    /**
     * Get status to current cob.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function getStatus() : ?string
    {
        return $this->status;
    }
    /**
     * Get tid to current cob.
     *
     * @since 2.0.0
     * @return string|null
     */
    public function getTid() : ?string
    {
        return $this->tid;
    }
    /**
     * Add pix to cob.
     *
     * @param Pix|array $pix
     * @since 3.0.0
     * @return self
     */
    public function addPix($pix)
    {
        $pix = $pix instanceof Pix ? $pix : (new Pix($pix['endToEndId'], $pix['valor']))->import($pix);
        $this->pixes[$pix->getE2eid()] = $pix;
        return $this;
    }
    /**
     * Get pix by e2eid.
     *
     * @param string $e2eid
     * @since 3.0.0
     * @return Pix|null
     */
    public function getPixBy(string $e2eid) : ?Pix
    {
        return $this->pixes[$e2eid] ?? null;
    }
    /**
     * Get all refunds associated to pix transaction.
     *
     * @since 3.0.0
     * @return array<Pix>
     */
    public function getPixes() : array
    {
        return $this->pixes;
    }
    /**
     * Is this cob paid?
     *
     * @since 3.0.0
     * @return bool
     */
    public function isPaid() : bool
    {
        if (static::isStatus(static::STATUS_FINISHED, $this->status)) {
            return \true;
        }
        if (empty($this->getPix()) === \false) {
            return empty($this->getPix()->getE2eid()) === \false;
        }
        return \false;
    }
    /**
     * Export this object to an array.
     *
     * @since 2.0.0
     * @return array
     */
    public function export() : array
    {
        $array = [];
        if (isset($this->calendar)) {
            $array['calendario'] = $this->calendar->export();
        }
        if (isset($this->receiver)) {
            $array[$this->receiver->getType()] = $this->receiver->export();
        }
        if (isset($this->debtor)) {
            $array[$this->debtor->getType()] = $this->debtor->export();
        }
        if (isset($this->amount)) {
            $array['valor'] = $this->amount->export();
        }
        if (isset($this->pixKey)) {
            $array['chave'] = $this->pixKey;
        }
        if (isset($this->requestToDebtor)) {
            $array['solicitacaoPagador'] = $this->requestToDebtor;
        }
        if (isset($this->revision)) {
            $array['revisao'] = $this->revision;
        }
        if (!empty($this->extra)) {
            $array['infoAdicionais'] = [];
            foreach ($this->extra as $name => $value) {
                $array['infoAdicionais'][] = ['nome' => $name, 'valor' => $value];
            }
        }
        if (isset($this->tid)) {
            $array['txid'] = $this->tid;
        }
        if (isset($this->status)) {
            $array['status'] = $this->status;
        }
        if (isset($this->location)) {
            $array['loc'] = $this->location->export();
        }
        if (empty($this->pixes) === \false) {
            $array['pix'] = [];
            foreach ($this->pixes as $pix) {
                $array['pix'][] = $pix->export();
            }
        }
        return $array;
    }
    /**
     * Import data from array.
     *
     * @param array $response
     * @since 2.0.0
     * @return self
     */
    public function import(array $response)
    {
        // Base data
        if (empty($response['chave']) === \false) {
            $this->setPixKey($response['chave']);
        }
        if (empty($response['pixCopiaECola']) === \false) {
            $this->setPixCopyPaste($response['pixCopiaECola']);
        }
        if (empty($response['solicitacaoPagador']) === \false) {
            $this->setRequestToDebtor($response['solicitacaoPagador']);
        }
        if (empty($response['infoAdicionais']) === \false && \is_array($response['infoAdicionais'])) {
            foreach ($response['infoAdicionais'] as $info) {
                $this->addExtra($info['nome'], $info['valor']);
            }
        }
        // Cob requested
        if (empty($response['calendario']) === \false && \is_array($response['calendario'])) {
            $this->setCalendar((new Calendar())->import($response['calendario']));
        }
        if (empty($response['devedor']) === \false && \is_array($response['devedor'])) {
            $this->setDebtor((new Person(Person::TYPE_DEBTOR, $response['devedor']['nome'], $response['devedor']['cpf'] ?? $response['devedor']['cnpj']))->import($response['devedor']));
        }
        if (empty($response['loc']) === \false) {
            $this->setLocation((new Location())->import($response['loc']));
        }
        if (empty($response['valor']) === \false) {
            $this->setAmount((new Amount())->import($response['valor']));
        }
        // Cob response
        if (empty($response['txid']) === \false) {
            $this->setTid($response['txid']);
        }
        if (isset($response['revisao']) || empty($response['revisao']) === \false) {
            $this->setRevision(\intval($response['revisao']));
        }
        if (empty($response['status']) === \false) {
            $this->setStatus($response['status']);
        }
        if (empty($response['recebedor']) === \false && \is_array($response['recebedor'])) {
            $this->setReceiver((new Person(Person::TYPE_RECEIVER, $response['recebedor']['nome'], $response['recebedor']['cpf'] ?? $response['recebedor']['cnpj']))->import($response['recebedor']));
        }
        if (empty($response['pix']) === \false && \is_array($response['pix'])) {
            $pixes = isset($response['pix'][0]) ? $response['pix'] : [$response['pix']];
            foreach ($pixes as $pix) {
                $this->addPix($pix);
            }
        }
        return $this;
    }
    /**
     * Throw an exception if $status is a invalid status.
     *
     * @param string $status
     * @since 2.0.0
     * @return void
     * @throws RuntimeException If is a invalid status.
     */
    public static function validateStatus(string $status)
    {
        if (\in_array($status, static::STATUSES, \true) === \false) {
            throw new RuntimeException(\sprintf('O status deve ser um dos seguintes: `%s`.', \implode('`, `', static::STATUSES)));
        }
    }
    /**
     * Is $expected equal to $actual.
     *
     * @param string $expected
     * @param string $actual
     * @since 2.0.0
     * @return boolean
     * @throws RuntimeException If some is a invalid status.
     */
    public static function isStatus(string $expected, string $actual) : bool
    {
        if (\in_array($expected, static::STATUSES, \true) === \false) {
            throw new RuntimeException(\sprintf('O status esperado deve ser um dos seguintes: `%s`.', \implode('`, `', static::STATUSES)));
        }
        if (\in_array($actual, static::STATUSES, \true) === \false) {
            throw new RuntimeException(\sprintf('O status atual deve ser um dos seguintes: `%s`.', \implode('`, `', static::STATUSES)));
        }
        return $expected === $actual;
    }
    /**
     * Throw an exception if $type is a invalid type.
     *
     * @param string $type
     * @since 2.0.0
     * @return void
     * @throws RuntimeException If is a invalid type.
     */
    public static function validateType(string $type)
    {
        if (\in_array($type, static::TYPES, \true) === \false) {
            throw new RuntimeException(\sprintf('O tipo de cobrança deve ser um dos seguintes: `%s`.', \implode('`, `', static::TYPES)));
        }
    }
    /**
     * Is $expected equal to $actual.
     *
     * @param string $expected
     * @param string $actual
     * @since 2.0.0
     * @return boolean
     * @throws RuntimeException If some is a invalid type.
     */
    public static function isType(string $expected, string $actual) : bool
    {
        if (\in_array($expected, static::TYPES, \true) === \false) {
            throw new RuntimeException(\sprintf('O tipo de cobrança esperado deve ser um dos seguintes: `%s`.', \implode('`, `', static::TYPES)));
        }
        if (\in_array($actual, static::TYPES, \true) === \false) {
            throw new RuntimeException(\sprintf('O tipo de cobrança atual deve ser um dos seguintes: `%s`.', \implode('`, `', static::TYPES)));
        }
        return $expected === $actual;
    }
}
