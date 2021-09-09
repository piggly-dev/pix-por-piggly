<?php

/**
 * Class Kanji
 *
 * @filesource   Kanji.php
 * @created      25.11.2015
 * @package      chillerlan\QRCode\Data
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */
namespace Piggly\WooPixGateway\Vendor\chillerlan\QRCode\Data;

use Piggly\WooPixGateway\Vendor\chillerlan\QRCode\QRCode;
use function mb_strlen, ord, sprintf, strlen;
/**
 * Kanji mode: double-byte characters from the Shift JIS character set
 */
class Kanji extends QRDataAbstract
{
    /**
     * @inheritdoc
     */
    protected $datamode = QRCode::DATA_KANJI;
    /**
     * @inheritdoc
     */
    protected $lengthBits = [8, 10, 12];
    /**
     * @inheritdoc
     */
    protected function getLength(string $data) : int
    {
        return mb_strlen($data, 'SJIS');
    }
    /**
     * @inheritdoc
     */
    protected function write(string $data) : void
    {
        $len = strlen($data);
        for ($i = 0; $i + 1 < $len; $i += 2) {
            $c = (0xff & ord($data[$i])) << 8 | 0xff & ord($data[$i + 1]);
            if (0x8140 <= $c && $c <= 0x9ffc) {
                $c -= 0x8140;
            } elseif (0xe040 <= $c && $c <= 0xebbf) {
                $c -= 0xc140;
            } else {
                throw new QRCodeDataException(sprintf('illegal char at %d [%d]', $i + 1, $c));
            }
            $this->bitBuffer->put(($c >> 8 & 0xff) * 0xc0 + ($c & 0xff), 13);
        }
        if ($i < $len) {
            throw new QRCodeDataException(sprintf('illegal char at %d', $i + 1));
        }
    }
}
