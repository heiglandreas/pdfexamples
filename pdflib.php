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

/**
 * This file creates a PDF-Invoice using PDFlib
 */

function create_pdf()
{
    $invoice = pdftest_getInvoiceText();
    $address = 'PHP-Usergroup Frankfurt/Main
    z.Hd. Herrn Christian Nielebock
    c/o DMR Solutions GmbH
    Siemensstraße 4

    63215 Bad Homburg';

    $pdf = new PDFlib();
    $pdf->set_option('stringformat=utf8');
    $pdf->set_option('resourcefile={' . realpath(__DIR__ . '/fonts/pdflib.upr') . '}');
    $pdf->set_option('charref=true');
    //$pdf->set_option('FontOutline={Calibri=/Volumes/Sites/Sites/pdflibs/fonts/lmroman10-regular.otf}');

    $pdf->begin_document('', 'pdfua=PDF/UA-1 pdfa=PDF/A-2a lang=de tag={tagname=Document Title={Rechnung}}');

    // Set the document-information
    $pdf->set_info ( 'Creator',  'Andreas Heigl @ PHP-Usergroup FFM');
    $pdf->set_info ( 'Author',   'Andreas Heigl');
    $pdf->set_info ( 'Title',    'Rechnung' );
    $pdf->set_info ( 'Subject',  'Rechnung erstellt mit PDFlib' );
    $pdf->set_info ( 'Keywords', 'PDFlib, PDF, Rechnung, phpugffm, test');

    $div = $pdf->begin_item('Div', 'Title={Rechnung}');
    // Start new Page
    $pdf->begin_page_ext(595, 842, '');
    $tag_header = $pdf->begin_item('Sect', 'Title={Rechnungskopf}');

    // Create page "header" for the first page
    $pdf->fit_textline('Andreas Heigl • Forsthausstraße 7 • 61279 Grävenwiesbach', 70, 670, 'position={left bottom} tag={tagname=Artifact} encoding=unicode fontname=Calibri fontsize=7 embedding=true');
    $pdf->setLineWidth(0.13);
    //$pdf->moveto(70, 668);
    //$pdf->lineto(280, 668);
    //$pdf->stroke();
    $image = $pdf->load_graphics('auto', __DIR__ . '/pentagramma.svg', '');
    $pdf->fit_graphics($image, 485, 732, 'position={top right} boxsize={100 100} fitmethod=auto tag={tagname=Figure Alt={Logo von Andreas Heigl}}');
    $header = $pdf->create_textflow('Andreas Heigl • Forsthausstraße 7 • 61279 Grävenwiesbach • Germany • Fon: +49 6083 959030 • Mail: andreas@heigl.org • XMPP: heiglandreas@jabber.org • IBAN: D32xxxxxxxxxxxxxxxxxxxxxxxxxxxxx • BIC: yyyyyyyyy', 'encoding=unicode fontname=CalibriLight fontsize=7 leading=100% alignment=justify embedding=true');
    $pdf->fit_textflow($header, 570, 20, 585, 700, 'orientate=west tag={tagname=Artifact}');

    // Adresse hinzufügen
    $addressFlow = $pdf->create_textflow($address, 'encoding=unicode fontname=Calibri fontsize=10 leading=130% alignment=left embedding=true');
    $pdf->fit_textflow($addressFlow, 70, 570, 280, 670, 'tag={tagname=Artifact}');
    $pdf->end_item($tag_header);
    $tag_copy = $pdf->begin_item('Sect', 'Title={Rechnungsinhalt}');
    // Create the content and start inserting it on the first page
    $textflow = $pdf->create_textflow($invoice, 'encoding=unicode fontname=Calibri fontsize=10 leading=100% alignment=justify errorpolicy=exception embedding=true');
    $overflow = $pdf->fit_textflow($textflow, 70, 20, 475, 600, 'tag={tagname=P}');

    // Continue placing text on follow-up-pages
    while ('_boxfull' == $overflow) {
        // End the current page
        $pdf->end_page_ext('');
        // start a new page
        $pdf->begin_page_ext(595, 842, '');

        // Create the page "header" of a follow-up-page
        $image = $pdf->load_graphics('auto', __DIR__ . '/pentagramma.svg', '');
        $pdf->fit_graphics($image, 555, 802, 'position={top right} boxsize={30 30} fitmethod=auto tag={tagname=Artifact}');
        $header = $pdf->create_textflow('Andreas Heigl • Forsthausstraße 7 • 61279 Grävenwiesbach • Germany • Fon: +49 6083 959030 • Mail: andreas@heigl.org • XMPP: heiglandreas@jabber.org • IBAN: D32xxxxxxxxxxxxxxxxxxxxxxxxxxxxx • BIC: yyyyyyyyy', 'encoding=unicode fontname=CalibriLight fontsize=7 leading=100% alignment=justify embedding=true');
        $pdf->fit_textflow($header, 570, 20,585, 700, 'orientate=west tag={tagname=Artifact}');

        // Place the remaining text
        $overflow = $pdf->fit_textflow($textflow, 70, 20, 475, 802, 'tag={tagname=P}');
    }

    // End the last page
    $pdf->end_page_ext('');

    $pdf->end_item($tag_copy);
    $pdf->end_item($div);

    // End the document
    $pdf->end_document('');

    $document = $pdf->get_buffer();

    return $document;
}
//$time = microtime(true);
//for ($i = 0; $i < 50; $i++) {
//    create_pdf();
//}
//echo sprintf("Creating %s PDF using PDFlib: %.5f\n", $i, (microtime(true)-$time));

    // Get the PDF-File as string
$document = create_pdf();
//
//
header('Content-Type: application/pdf');
header('Content-Length: ' . strlen($document));
////header('Content-Disposition: attachment; inline="PDFlib-Rechnung"');
echo $document;


function pdftest_getInvoiceText()
{
    return '<macro {
    default { leftindent=0 rightindent=0 leading=100% parindent=0 fontname=Calibri fontsize=10 encoding=unicode embedding=true  }
    align-right { alignment=right  }
    strong { fontname=CalibriBold embedding=true encoding=unicode  }
    align-left { alignment=left  }
    copy { leading=120% leftindent=0 rightindent=0 parindent=0 fontname=Calibri embedding=true fontsize=10 encoding=unicode  }
    subject { leading=100% fontsize=12  }
    table { ruler={20 285 345 405} tabalignment={left right right right} hortabmethod=ruler leading=140%  }  }>
<&align-right><&strong>Rechnungs-Nummer: <&default>12345
<&strong>Datum: <&default>22. Jan 2014
<&strong>Ihr Zeichen: <&default>PHP-UG-FFM 1
<&align-left><&strong><&subject>Rechnung

<&copy>Sehr geehrte Damen und Herren.
Folgende Leistungen erlaube ich mir für den Januar 2014 in Rechnung zu stellen:

<&table><&strong>Pos.	Leistung	Einh.	Kosten	Summe
<&copy>1.	Luftnummern	15 Stück	200,00 €	3000,00 €
2.	Lachnummern	22 Stück	17,23 €	379,06 €
3.	Lachnummern	22 Stück	17,23 €	379,06 €
4.	Lachnummern	22 Stück	17,23 €	379,06 €
5.	Lachnummern	22 Stück	17,23 €	379,06 €
6.	Lachnummern	22 Stück	17,23 €	379,06 €
7.	Lachnummern	22 Stück	17,23 €	379,06 €
8.	Lachnummern	22 Stück	17,23 €	379,06 €
9.	Lachnummern	22 Stück	17,23 €	379,06 €
10.	Lachnummern	22 Stück	17,23 €	379,06 €
11.	Lachnummern	22 Stück	17,23 €	379,06 €
12.	Lachnummern	22 Stück	17,23 €	379,06 €
13.	Lachnummern	22 Stück	17,23 €	379,06 €
14.	Lachnummern	22 Stück	17,23 €	379,06 €
15.	Lachnummern	22 Stück	17,23 €	379,06 €
16.	Lachnummern	22 Stück	17,23 €	379,06 €
17.	Lachnummern	22 Stück	17,23 €	379,06 €
18.	Lachnummern	22 Stück	17,23 €	379,06 €
19.	Lachnummern	22 Stück	17,23 €	379,06 €
20.	Lachnummern	22 Stück	17,23 €	379,06 €
21.	Lachnummern	22 Stück	17,23 €	379,06 €
22.	Lachnummern	22 Stück	17,23 €	379,06 €
23.	Lachnummern	22 Stück	17,23 €	379,06 €
24.	Lachnummern	22 Stück	17,23 €	379,06 €
25.	Lachnummern	22 Stück	17,23 €	379,06 €
26.	Lachnummern	22 Stück	17,23 €	379,06 €
27.	Lachnummern	22 Stück	17,23 €	379,06 €
28.	Lachnummern	22 Stück	17,23 €	379,06 €
29.	Lachnummern	22 Stück	17,23 €	379,06 €
30.	Lachnummern	22 Stück	17,23 €	379,06 €
31.	Lachnummern	22 Stück	17,23 €	379,06 €
32.	Lachnummern	22 Stück	17,23 €	379,06 €
33.	Lachnummern	22 Stück	17,23 €	379,06 €
34.	Lachnummern	22 Stück	17,23 €	379,06 €
35.	Lachnummern	22 Stück	17,23 €	379,06 €
36.	Lachnummern	22 Stück	17,23 €	379,06 €
37.	Lachnummern	22 Stück	17,23 €	379,06 €
38.	Lachnummern	22 Stück	17,23 €	379,06 €
39.	Lachnummern	22 Stück	17,23 €	379,06 €
40.	Lachnummern	22 Stück	17,23 €	379,06 €
41.	Lachnummern	22 Stück	17,23 €	379,06 €
42.	Lachnummern	22 Stück	17,23 €	379,06 €
43.	Lachnummern	22 Stück	17,23 €	379,06 €
44.	Lachnummern	22 Stück	17,23 €	379,06 €
45.	Lachnummern	22 Stück	17,23 €	379,06 €
46.	Lachnummern	22 Stück	17,23 €	379,06 €
47.	Lachnummern	22 Stück	17,23 €	379,06 €
48.	Lachnummern	22 Stück	17,23 €	379,06 €
49.	Lachnummern	22 Stück	17,23 €	379,06 €
50.	Lachnummern	22 Stück	17,23 €	379,06 €
51.	Lachnummern	22 Stück	17,23 €	379,06 €
52.	Lachnummern	22 Stück	17,23 €	379,06 €
53.	Lachnummern	22 Stück	17,23 €	379,06 €
54.	Lachnummern	22 Stück	17,23 €	379,06 €
55.	Lachnummern	22 Stück	17,23 €	379,06 €
	Gesamtsumme:			<&strong>23.000 €

<&copy>Bitte überweisen Sie den fälligen Endbetrag innerhalb 14 Tagen auf unser Konto.

Mit freundlichen Grüßen

Andreas Heigl';

}