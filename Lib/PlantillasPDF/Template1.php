<?php
/**
 * Copyright (C) 2019-2024 Carlos Garcia Gomez <carlos@facturascripts.com>
 */

namespace FacturaScripts\Plugins\AnticiposPlantillasPDFsalesDoc\Lib\PlantillasPDF;

use FacturaScripts\Core\Tools;
use FacturaScripts\Dinamic\Lib\PlantillasPDF\Helper\PaymentMethodBankDataHelper;
use FacturaScripts\Dinamic\Lib\PlantillasPDF\Helper\ReceiptBankDataHelper;

use FacturaScripts\Plugins\PlantillasPDF\Lib\PlantillasPDF\Template1 as AdvancesTemplate1;
/**
 * Description of Template1
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 * @author Jorge-Prebac              <info@smartcuines.com>
 */
class Template1 extends AdvancesTemplate1
{
    /**
     * @param BusinessDocument|FacturaCliente $model
     */
    public function addInvoiceFooter($model)
    {
        $i18n = Tools::lang();
		
		if ($model->codcliente) {
			$this->addAdvancesDoc($model, $i18n);
		}

        $receipts = $model->modelClassName() === 'FacturaCliente' && !$this->get('hidereceipts') && !$this->format->hidetotals && !$this->format->hidereceipts
            ? $model->getReceipts() : [];

        if ($receipts) {
            $trs = '<thead>'
                . '<tr>'
                . '<th>' . $i18n->trans('receipt') . '</th>';
            if (!$this->get('hidepaymentmethods') && !$this->format->hidepaymentmethods) {
                $trs .= '<th>' . $i18n->trans('payment-method') . '</th>';
            }

            $trs .= '<th align="right">' . $i18n->trans('amount') . '</th>';
            if (!$this->get('hideexpirationpayment')) {
                $trs .= '<th align="right">' . $i18n->trans('expiration') . '</th>';
            }

            $trs .= '</tr>'
                . '</thead>';
            foreach ($receipts as $receipt) {
                $expiration = $receipt->pagado ? $i18n->trans('paid') : $receipt->vencimiento;
                $expiration .= $this->get('showpaymentdate') ? ' ' . $receipt->fechapago : '';

                $payLink = empty($receipt->url('pay')) ? '' :
                    ' <a href="' . $receipt->url('pay') . '&mpdf=.html">' . $i18n->trans('pay') . '</a>';

                $trs .= '<tr>'
                    . '<td align="center">' . $receipt->numero . '</td>';
                if (!$this->get('hidepaymentmethods') && !$this->format->hidepaymentmethods) {
                    $trs .= '<td align="center">' . ReceiptBankDataHelper::get($receipt, $receipts) . $payLink . '</td>';
                }

                $trs .= '<td align="right">' . Tools::money($receipt->importe, $model->coddivisa) . '</td>';
                if (!$this->get('hideexpirationpayment')) {
                    $trs .= '<td align="right">' . $expiration . '</td>';
                }

                $trs .= '</tr>';
            }

            $this->writeHTML('<table class="table-big table-list">' . $trs . '</table>');
        } elseif (isset($model->codcliente) && false === $this->format->hidetotals && !$this->get('hidepaymentmethods') && !$this->format->hidepaymentmethods) {
            $expiration = $model->finoferta ?? '';
            $trs = '<thead>'
                . '<tr>'
                . '<th align="left">' . $i18n->trans('payment-method') . '</th>';

            if (!$this->get('hideexpirationpayment') && !$this->get('hidereceipts') && !$this->format->hidereceipts) {
                $trs .= '<th align="right">' . $i18n->trans('expiration') . '</th>';
            }

            $trs .= '</tr>'
                . '</thead>'
                . '<tr>'
                . '<td align="left">' . PaymentMethodBankDataHelper::get($model) . '</td>';
            if (!$this->get('hideexpirationpayment') && !$this->get('hidereceipts') && !$this->format->hidereceipts) {
                $trs .= '<td align="right">' . $expiration . '</td>';
            }

            $trs .= '</tr>';
            $this->writeHTML('<table class="table-big table-list">' . $trs . '</table>');
        }

        $this->writeHTML($this->getImageText());

        if (!empty($this->get('endtext'))) {
            $paragraph = '<p class="end-text">' . nl2br($this->get('endtext')) . '</p>';
            $this->writeHTML($paragraph);
        }
    }

	public function addAdvancesDoc($model, $i18n)
	{
		$advances = $model->modelClassName() !== 'FacturaCliente' && !$this->get('hidereceipts') && !$this->format->hidetotals && !$this->format->hidereceipts
			? $model->getAdvances() : [];

		if ($advances) {
			$trs = '<thead>'
				. '<tr>'
				. '<th>' . $i18n->trans('advance-payment') . '</th>'
				. '<th align="right">' . $i18n->trans('amount') . '</th>';
			if (!$this->get('hideexpirationpayment')) {
				$trs .= '<th align="right">' . $i18n->trans('expiration') . '</th>';
			}

			$trs .= '</tr>'
				. '</thead>';
			foreach ($advances as $advance) {
				$expiration = $i18n->trans('paid') . ' ' . $advance->fecha;
				$trs .= '<tr>';
				$trs .= '<td align="center">' . $advance->id . '</td>';
				$trs .= '<td align="right">' . Tools::money($advance->importe, $model->coddivisa) . '</td>';
				if (!$this->get('hideexpirationpayment')) {
					$trs .= '<td align="right">' . $expiration . '</td>';
				}

				$trs .= '</tr>';
			}

			$this->writeHTML('<table class="table-big table-list">' . $trs . '</table>');
		}
	}
}
