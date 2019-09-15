<?php

namespace AS2\Tests\Unit;

use AS2\Tests\TestCase;
use AS2\MimePart;

class MimePartTest extends TestCase
{
    public function testIsEncrypted()
    {
        $mimePart = new MimePart(
            [
                'Content-Type' => 'application/pkcs7-mime; name="smime.p7m"; smime-type=enveloped-data',
            ]
        );
        $this->assertTrue($mimePart->isPkc7Mime());
        $this->assertTrue($mimePart->isEncrypted());
    }

    public function testIsCompressed()
    {
        $mimePart = new MimePart(
            [
                'Content-Type' => 'application/pkcs7-mime; name="smime.p7m"; smime-type=compressed-data',
            ]
        );
        $this->assertTrue($mimePart->isCompressed());
    }

    public function testIsSigned()
    {
        $mimePart = new MimePart(
            [
                'Content-Type' => 'multipart/signed;protocol="application/pkcs7-signature";micalg=sha1;boundary="_=A=_"',
            ]
        );
        $this->assertTrue($mimePart->isSigned());
    }

    public function testIsPkc7Signature()
    {
        $mimePart = new MimePart(
            [
                'Content-Type' => 'Application/pkcs7-signature;name=EDIINTSIG.p7s',
            ]
        );
        $this->assertTrue($mimePart->isPkc7Signature());
    }

    public function testIsReport()
    {
        $mimePart = new MimePart(
            [
                'Content-Type' => 'multipart/report;Report-Type=disposition-notification;',
            ]
        );
        $this->assertTrue($mimePart->isReport());
    }

    public function testIsMultipart()
    {
        $boundary = '_=A=_';

        $mimePart = new MimePart(
            [
                'Content-Type' => 'multipart/mixed; boundary="'.$boundary.'"',
            ]
        );
        $mimePart->addPart("1");
        $mimePart->addPart("2");;

        $this->assertTrue($mimePart->isMultiPart());
        $this->assertEquals($boundary, $mimePart->getParsedHeader('content-type', 0, 'boundary'));
    }

    public function testBody()
    {
        $mimePart = MimePart::fromString("content-type:text/plain;\n\ntest");
        $this->assertEquals('test', $mimePart->getBody());

        $mimePart->setBody("test2");
        $this->assertEquals('test2', $mimePart->getBody());

        $mimePart = MimePart::fromString("content-type:multipart/mixed;\r\n\r\ntest");
        $this->assertEquals('test', $mimePart->getBody());

        $mimePart->setBody(new MimePart([], '1'));
        $this->assertTrue($mimePart->getCountParts() == 1);

        $mimePart->setBody(['2', '3',]);
        $this->assertTrue($mimePart->getCountParts() == 3);
    }
}
