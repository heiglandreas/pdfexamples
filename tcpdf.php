<?php
/**
 * Copyright (c)2014-2014 heiglandreas
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIBILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category 
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2014-2014 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     20.01.14
 * @link      https://github.com/heiglandreas/
 */

include_once 'vendor/autoload.php';

class MyPDF extends TCPDF
{

    protected $recipient = '';

    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=true)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->reg = $this->addTTFFont(__DIR__ . '/fonts/Calibri.ttf', '', '', 32/*, __DIR__ . '/tcpdf-fonts/'*/);
        $this->bol = $this->addTTFFont(__DIR__ . '/fonts/Calibri Bold.ttf', '', '', 32/*, __DIR__ . '/tcpdf-fonts/'*/);
        $this->thi = $this->addTTFFont(__DIR__ . '/fonts/CalibriL.ttf', '', '', 32/*, __DIR__ . '/tcpdf-fonts/'*/);
    }

    public function header()
    {
        $this->setFont($this->thi, '', 7);
        $this->startTransform();
        $this->rotate(90, 203.5, 290);
        $this->writeHTMLCell(283, 7, 203.5, 290, 'Andreas Heigl • Forsthausstraße 7 • 61279 Grävenwiesbach • Germany • Fon: +49 6083 959030 • Mail: andreas@heigl.org • XMPP: heiglandreas@jabber.org • IBAN: D32xxxxxxxxxxxxxxxxxxxxxxxxxxxxx • BIC: yyyyyyyyy');
        $this->stopTransform();
        if (1 == $this->getPage()) {
            $this->setFont($this->reg, '', 7);
            $this->setY(60);
            $this->cell(0, 15, 'Andreas Heigl • Forsthausstraße 7 • 61279 Grävenwiesbach', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $this->ImageSVG($file='pentagramma.svg', $x=171.09, $y=4, $w=35.28, $h=35.28, $link='', $align='', $palign='', $border=0, $fitonpage=false);

            $this->setFont($this->reg, '', 10);
            $this->writeHTMLCell(80, 60, 25, 62.5, $this->recipient);
            $this->writeHTMLCell(60, 60, 114.73, 62.5, $this->getMetaInfos());
            $this->setY(100);
            $this->setFont($this->reg, 'B', 10);
            $this->cell(0, 15, $this->getSubject());

        } else {
            $this->setFont($this->reg, '', 10);
            $this->setMargins(25, 7, 35.27, 7);
            $this->ImageSVG($file='pentagramma.svg', $x=195.5, $y=4, $w=10.5, $h=10.5, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        }

    }

    public function footer()
    {

    }

    protected function getMetaInfos()
    {
        return '<strong>Rechnungs-Nr:</strong> 12345<br/>
<strong>Datum: </strong>' . date('d. m. Y') . '<br/>
<strong>Ihr Zeichen: </strong> PHP-UG-FFM 1';
    }

    protected function getSubject()
    {
        return 'Rechnung';
    }
}

function create_pdf()
{
    /**
     * This file creates aPDF-Invoice using TCPDF
     */
    $pdf = new MyPDF('A4', 'mm', 'portrait', true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator('Andreas Heigl @ PHP-Usergroup FFM');
    $pdf->SetAuthor('Andreas Heigl');
    $pdf->SetTitle('Rechnung');
    $pdf->SetSubject('REchnung erstellt mit TCPDF');
    $pdf->SetKeywords('TCPDF, PDF, Rechnung, phpugffm, test');

    $pdf->setRecipient('PHP-Usergroup Frankfurt/Main<br/>
    z.Hd. Herrn Christian Nielebock<br/>
    c/o DMR Solutions GmbH<br/>
    Siemensstraße 4<br/><br/>

    <strong>63215 Bad Homburg</strong>');

    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);

    // set header and footer fonts
    $pdf->setHeaderFont(Array('dejavusans', '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array('dejavusans', '', PDF_FONT_SIZE_DATA));

    // set margins
    $pdf->SetMargins(25, 110, 35.27, 7);
    $pdf->SetHeaderMargin(7);
    $pdf->SetFooterMargin(7);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 7);

    // ---------------------------------------------------------

    // set default font subsetting mode
    $pdf->setFontSubsetting(false);

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 10, '', true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    // set text shadow effect
    //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', pdftest_getInvoiceText(), 0, 1, 0, true, '', true);

    return $pdf;
}
// ---------------------------------------------------------
//$time = microtime(true);
//for ($i=0; $i< 10; $i++) {
//    create_pdf();
//}
//echo sprintf("Creating %s PDF using TCPDF: %.5f\n", $i, (microtime(true)-$time));
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
create_pdf()->Output('example_001.pdf', 'I');

function pdftest_getInvoiceText()
{
    return '<h1 style="font-size:10pt;font-weight:500;">Sehr geehrte Damen und Herren.</h1>
<p>Folgende Leistungen erlaube ich mir für den Januar 2014 in Rechnung zu stellen:</p>
<table>
<thead>
<tr><th style="width:25px;">Pos</th><th style="width:220pt;">Leistung</th><th style="width:60pt;">Einh.</th><th style="width:60pt;">Kosten</th><th style="width:60pt;">Summe</th>
</tr></thead><tbody>
<tr><td style="width:20pt;">1.</td><td style="width:225pt;">Luftnummern</td><td style="width:60pt;">15 Stück</td><td style="width:60pt;text-align:right">200,00 €</td><td style="width:60pt;text-align:right">3000,00 €</td></tr>
<tr><td>2.</td><td>Lachnummern</td><td>22 Stück</td><td style="text-align:right">17,23 €</td><td style="text-align:right">379,06 €</td></tr>
<tr><td>3.</td><td>Luftnummern</td><td>15 Stück</td><td style="text-align:right">200,00 €</td><td style="text-align:right">3000,00 €</td></tr>
<tr><td>4.</td><td>Lachnummern</td><td>22 Stück</td><td style="text-align:right">17,23 €</td><td style="text-align:right">379,06 €</td></tr>
<tr><td>5.</td><td>Luftnummern</td><td>15 Stück</td><td style="text-align:right">200,00 €</td><td style="text-align:right">3000,00 €</td></tr>
<tr><td>6.</td><td>Lachnummern</td><td>22 Stück</td><td style="text-align:right">17,23 €</td><td style="text-align:right">379,06 €</td></tr>
<tr><td>7.</td><td>Luftnummern</td><td>15 Stück</td><td style="text-align:right">200,00 €</td><td style="text-align:right">3000,00 €</td></tr>
<tr><td>8.</td><td>Lachnummern</td><td>22 Stück</td><td style="text-align:right">17,23 €</td><td style="text-align:right">379,06 €</td></tr>
<tr><td>9.</td><td>Luftnummern</td><td>15 Stück</td><td style="text-align:right">200,00 €</td><td style="text-align:right">3000,00 €</td></tr>
<tr><td>10.</td><td>Lachnummern</td><td>22 Stück</td><td style="text-align:right">17,23 €</td><td style="text-align:right">379,06 €</td></tr>
<tr><td>11.</td><td>Luftnummern</td><td>15 Stück</td><td style="text-align:right">200,00 €</td><td style="text-align:right">3000,00 €</td></tr>
<tr><td>12.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>13.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>14.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>15.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>16.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>17.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>18.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>19.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>20.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>21.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>22.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>23.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>24.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>25.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>26.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>27.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>28.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>29.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>30.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>31.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>32.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>33.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>34.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>35.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>36.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>37.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>38.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>39.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>40.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>41.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>42.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>43.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>44.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>45.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>46.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>47.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td>48.</td><td>Lachnummern</td><td>22 Stück</td><td>17,23 €</td><td>379,06 €</td></tr>
<tr><td>49.</td><td>Luftnummern</td><td>15 Stück</td><td>200,00 €</td><td>3000,00 €</td></tr>
<tr><td></td><td>Gesamtsumme</td><td></td><td></td><td><strong>36000,00 €</strong></td></tr>
</tbody></table>
<p>Bitte überweisen Sie den fälligen Endbetrag innerhalb 14 Tagen auf unser Konto.</p>
<p>Mit freundlichen Grüßen</p>
<p>Andreas Heigl</p>';
}