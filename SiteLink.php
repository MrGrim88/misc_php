<?php



  class SiteLink {
	private $apiKey = '::API_KEY';
	private $slURL = 'https://api.smdservers.net/CCWs_3.5/CallCenterWs.asmx?WSDL';
	private $CORP_CODE, $LOC_CODE, $CORP_LOGIN, $CORP_PASS;
	private $client, $params;

	public function __construct($args = false,$report=false) {
	  $this->CORP_CODE = ($args['cCode'] != '' ? $args['cCode'] : '');
	  $this->LOC_CODE = ($args['lCode'] != '' ? $args['lCode'] : '');
	  $this->CORP_LOGIN = ($args['cLogin'] != '' ? $args['cLogin'] . $this->apiKey : '');
	  $this->CORP_PASS = ($args['cPass'] != '' ? $args['cPass'] : '');
      $this->FAC_ID = ($args['facility_id'] != '' ? $args['facility_id'] : '');
	  if ($report == true) {
		  $this->slURL = 'https://api.smdservers.net/CCWs_3.5/ReportingWs.asmx?WSDL';
	  }
	}
	public function setCredentials($cCode, $lCode, $cLogin, $cPass) {
	  $this->CORP_CODE = $cCode;
	  $this->LOC_CODE = $lCode;
	  $this->CORP_LOGIN = $cLogin . $this->apiKey;
	  $this->CORP_PASS = $cPass;
	}
	public function setAPIKey($aKey) {
        $this->apiKey = $aKey ?? $this->apiKey;
		return 0;
	}
	public function setSLURL($sURL) {
        $this->slURL = $sURL ?? $this->slURL;
		return 0;
	}
	public function initiateCall() {
		if ($this->CORP_CODE != '' || $this->CORP_LOGIN != '' || $this->CORP_PASS != '' || $this->LOC_CODE != '') {
			$this->client = new \SoapClient( $this->slURL );
			$this->params = new \stdClass();
			$this->params->sCorpCode = $this->CORP_CODE;
			$this->params->sLocationCode = $this->LOC_CODE;
			$this->params->sCorpUserName = $this->CORP_LOGIN;
			$this->params->sCorpPassword = $this->CORP_PASS;
			$this->params->lngLastTimePolled = '0';
			return 1;
		}
        return 0;
	}
	public function getJSON($data) {
	  $xml = simplexml_load_string($data);
	  $json = json_encode($xml);
	  return json_decode($json,TRUE);
	}
	public function getAvailableUnits() {
	  $avail_list = $this->client->UnitsInformationAvailableUnitsOnly_v2( $this->params );
	  $result = $avail_list->UnitsInformationAvailableUnitsOnly_v2Result;
	  return $this->getJSON($result->any);
	}
	public function getSiteInformation() {
		$avail_list = $this->client->SiteInformation( $this->params );
		$result = $avail_list->SiteInformationResult;
  	  return $this->getJSON($result->any);
	}
	public function getSiteInformationAjax() {
		$siteInfo = $this->getSiteInformation();
		if (!isset($siteInfo['NewDataSet']['Table'])) {
			return json_encode(['error'=>'Invalid Credentials'],true);
		}
	    $d = $siteInfo['NewDataSet']['Table'];
		$data['add1'] = $d['sSiteAddr1'];
		$data['add2'] = $d['sSiteAddr2'];
		$data['city'] = $d['sSiteCity'];
		$data['state'] = $d['sSiteRegion'];
		$data['zip'] = $d['sSitePostalCode'];
		$data['count'] = $d['sSiteCountry'];
		$data['lat'] = $d['dcLatitude'];
		$data['pho'] = $d['sSitePhone'];
		$data['lng'] = $d['dcLongitude'];
		$data['wds'] = $d['tWeekdayStrt'];
		$data['wde'] = $d['tWeekdayEnd'];
		$data['sas'] = $d['tSaturdayStrt'];
		$data['sae'] = $d['tSaturdayEnd'];
		$data['sus'] = $d['tSundayStrt'];
		$data['sue'] = $d['tSundayEnd'];
		return json_encode(['results'=>$data],true);
	}
	public function getPriceList() {
	  $avail_list = $this->client->UnitTypePriceList_v2( $this->params );
	  $result = $avail_list->UnitTypePriceList_v2Result;
	  return $this->getJSON($result->any);
	}
	public function getUnitsInformation() {
	  $avail_list = $this->client->UnitsInformation_v2( $this->params );
	  $result = $avail_list->UnitsInformation_v2Result;
	  return $this->getJSON($result->any);
	}
	public function getUnitInformationByName($unitName) {
	  $myParams = $this->params;
	  $myParams->sUnitName = $unitName;
	  $avail_list = $this->client->UnitsInformationByUnitName( $myParams );
	  $result = $avail_list->UnitsInformationByUnitNameResult;
	  return $this->getJSON($result->any);
	}
    public function getItems() {
        $avail_list = $this->client->POSItemsRetrieve( $this->params );
  	    $result = $avail_list->POSItemsRetrieveResult;
  	    return $this->getJSON($result->any);
    }
    public function updateItemStockQuantity($item, $in_stock) {
        $myParams = $this->params;
        $myParams->ChargeDescID = (int)$item;
        $myParams->dcInStock = (double)$in_stock;
        $avail_list = $this->client->POSItemUpdateInStockQuantity( $myParams );
        $result = $avail_list->POSItemUpdateInStockQuantityResult;
        return $this->getJSON($result->any);
    }
    public function addItemToLedger($ledger, $item, $price_pretax, $qty) {
        $myParams = $this->params;
        $myParams->LedgerID = (int)$ledger;
        $myParams->ChargeDescID = (int)$item;
        $myParams->dcPricePreTax = (double)$price_pretax;
        $myParams->iQuantity = (int)$qty;
        $avail_list = $this->client->POSItemAddToLedger( $myParams );
        $result = $avail_list->POSItemAddToLedgerResult;
        return $this->getJSON($result->any);
    }
    public function itemPaymentCheck($item, $amount, $check, $name, $address, $zip, $source = 10, $test = false) {
        $myParams = $this->params;
        $myParams->sChargeDescID = $item;
        $myParams->dcPaymentAmount = (double)$amount;
        $myParams->iPaymentMethod = 1;
        $myParams->sCheckNum = $check;
        $myParams->sBillingName = $name;
        $myParams->sBillingAddress = $address;
        $myParams->sBillingZipCode = $zip;
        $myParams->iSource = (int)$source;
        $myParams->bTestMode = (bool)$test;
        $avail_list = $this->client->POSItemPayment( $myParams );
        $result = $avail_list->POSItemPaymentResult;
        return $this->getJSON($result->any);
    }
    public function paymentTypes() {
        return json_encode([
            '0' => 'Credit Card',
            '1' => 'Check',
            '2' => 'Cash',
            '3' => 'Debit Card',
        ]);
    }
    public function ccTypes() {
        return json_encode([
            '5' => 'MasterCard',
            '6' => 'Visa',
            '7' => 'American Express',
            '8' => 'Discover',
            '9' => 'Diner\'s Club',
        ]);
    }
    public function paymentSources() {
        return json_encode([
            '2' => 'Kiosk',
            '3' => 'Call Center',
            '10' => 'Website',
        ]);
    }
    public function itemPaymentCredit($item, $amount, $ccType, $ccNo, $cvv,
    $exp, $name, $address, $zip, $source = 10, $test = false
    ) {
        $myParams = $this->params;
        $myParams->sChargeDescID = $item;
        $myParams->dcPaymentAmount = (double)$amount;
        $myParams->iPaymentMethod = 0;
        $myParams->iCreditCardType = (int)$ccType;;
        $myParams->sCreditCardNumber = $ccNo;
        $myParams->sCreditCardCVV = $cvv;
        $myParams->dExpirationDate = $exp;
        $myParams->sBillingName = $name;
        $myParams->sBillingAddress = $address;
        $myParams->sBillingZipCode = $zip;
        $myParams->iSource = (int)$source;
        $myParams->bTestMode = (bool)$test;
        $avail_list = $this->client->POSItemPayment( $myParams );
        $result = $avail_list->POSItemPaymentResult;
        return $this->getJSON($result->any);
    }
	public function getUnitInformationByID($unitID) {
	  $myParams = $this->params;
	  $myParams->iUnitID = $unitID;
	  $avail_list = $this->client->UnitsInformationByUnitID( $myParams );
	  $result = $avail_list->UnitsInformationByUnitIDResult;
	  return $this->getJSON($result->any);
	}
	public function getMoveInCostWithDiscount($unitID,$conID,$mDate,$covID) {
	  $myParams = $this->params;
	  $myParams->iUnitID = $unitID;
	  $myParams->dMoveInDate = $mDate;
	  $myParms->InsuranceCoverageID = $covID;
	  $myParams->ConcessionPlanID = $conID;
	  $avail_list = $this->client->MoveInCostRetrieveWithDiscount( $myParams );
	  $result = $avail_list->MoveInCostRetrieveWithDiscountResult;
	  return $this->getJSON($result->any);
	}
    public function getMoveInCost($unitID, $mDate) {
        $myParams = $this->params;
        $myParams->iUnitID = (int)$unitID;
        $myParams->dMoveInDate = $mDate;
        $avail_list = $this->client->MoveInCostRetrieve( $myParams );
        $result = $avail_list->MoveInCostRetrieveResult;
        return $this->getJSON($result->any);
    }
	public function getDiscountPlans() {
	  $avail_list = $this->client->DiscountPlansRetrieve( $this->params );
	  $result = $avail_list->DiscountPlansRetrieveResult;
	  return $this->getJSON($result->any);
	}
	public function getAllDiscountPlans() {
	  $avail_list = $this->client->DiscountPlansRetrieveIncludingDisabled( $this->params );
	  $result = $avail_list->DiscountPlansRetrieveIncludingDisabledResult;
	  return $this->getJSON($result->any);
	}
	public function getPaymentTypes() {
	  $avail_list = $this->client->PaymentTypesRetrieve( $this->params );
	  $result = $avail_list->PaymentTypesRetrieveResult;
	  return $this->getJSON($result->any);
	}
	public function getInsuranceCoverage() {
	  $avail_list = $this->client->InsuranceCoverageRetrieve( $this->params );
	  $result = $avail_list->InsuranceCoverageRetrieveResult;
	  return $this->getJSON($result->any);
	}
	public function getTenantList($firstName,$lastName) {
	  $myParams = $this->params;
	  $myParams->sTenantFirstName = $firstName;
	  $myParams->sTenantLastName = $lastName;

	  $avail_list = $this->client->TenantList( $myParams );
	  $result = $avail_list->TenantListResult;
	  return $this->getJSON($result->any);
	}
	public function getTenantInfoByID($tenID) {
	  $myParams = $this->params;
	  //TODO: Find exact name for Unit ID parameter
	  $myParams->iTenantID = $tenID;
	  $avail_list = $this->client->TenantInfoByTenantID( $myParams );
	  $result = $avail_list->TenantInfoByTenantIDResult;
	  return $this->getJSON($result->any);
	}
	public function getChargeDescriptions() {
	  $avail_list = $this->client->ChargeDescriptionsRetrieve( $this->params );
	  $result = $avail_list->ChargeDescriptionsRetrieveResult;
	  return $this->getJSON($result->any);
	}
	public function getChargesAndPaymentsByLedgerID($lID) {
	  $myParams = $this->params;
	  $myParams->sLedgerID = $lID;
	  $avail_list = $this->client->ChargesAndPaymentsByLedgerID( $myParams );
	  $result = $avail_list->ChargesAndPaymentsByLedgerIDResult;
	  return $this->getJSON($result->any);
	}
	public function getPromotions() {
	  $avail_list = $this->client->PromotionsRetrieve( $this->params );
	  $result = $avail_list->PromotionsRetrieveResult;
	  return $this->getJSON($result->any);
	}
    public function unitStandRateUpdate($units,$dRate) {
	  $myParams = $this->params;
	  $myParams->sUnitIDsCommaDelimited = $units;
	  $myParams->dcStdRate = $dRate;
	  $avail_list = $this->client->UnitStandardRateUpdate( $myParams );
	  $result = $avail_list->UnitStandardRateUpdateResult;
	  return $this->getJSON($result->any);
	}
    public function unitStandRateUpdate2($units,$dRate) {
	  $myParams = $this->params;
	  $myParams->sUnitIDsCommaDelimited = $units;
	  $myParams->dcStdRate = $dRate;
      $myParams->iRatesTaxInclusive = 0;
      $myParams->sUsagePassword = 'UnitStandardRateP@SS';
	  $avail_list = $this->client->UnitStandardRateUpdate_v2( $myParams );
	  $result = $avail_list->UnitStandardRateUpdate_v2Result;
	  return $this->getJSON($result->any);
	}
    public function createLeaseURL($tenant_id, $ledger_id) {
        $myParams = $this->params;
        $myParams->iTenantID = (int)$tenant_id;
        $myParams->iLedgerID = (int)$ledger_id;
  	    $avail_list = $this->client->SiteLinkeSignCreateLeaseURL( $myParams );
  	    $result = $avail_list->SiteLinkeSignCreateLeaseURLResult;
  	    return $this->getJSON($result->any);
    }
    public function createDocumentURL($tenant_id, $ledger_id, $form_id) {
        $myParams = $this->params;
        $myParams->iTenantID = (int)$tenant_id;
        $myParams->iLedgerID = (int)$ledger_id;
        $myParams->iFormId = (int)$form_id;
  	    $avail_list = $this->client->SiteLinkeSignCreateDocumentURL( $myParams );
  	    $result = $avail_list->SiteLinkeSignCreateDocumentURLResult;
  	    return $this->getJSON($result->any);
    }
    public function generateESignDownload($doc_id) {
        $myParams = $this->params;
        $myParams->documentId = (string)$doc_id;
  	    $avail_list = $this->client->SiteLinkeSignGenerateDownloadUrl( $myParams );
  	    $result = $avail_list->SiteLinkeSignGenerateDownloadUrlResult;
  	    return $this->getJSON($result->any);
    }
    public function getForms() {
        $avail_list = $this->client->FormsRetrieve( $this->params );
	    $result = $avail_list->FormsRetrieveResult;
  	    return $this->getJSON($result->any);
    }
    public function chargesByLedger($ledger) {
        $myParams = $this->params;
        $myParams->ledgerId = (int)$ledger;
  	    $avail_list = $this->client->ChargesAllByLedgerID( $myParams );
  	    $result = $avail_list->ChargesAllByLedgerIDResult;
  	    return $this->getJSON($result->any);
    }
    public function previewLease($unit) {
        $myParams = $this->params;
        $myParams->iUnitID = (int)$unit;
  	    $avail_list = $this->client->SiteLinkeSignPreviewLeaseURL( $myParams );
  	    $result = $avail_list->SiteLinkeSignPreviewLeaseURLResult;
  	    return $this->getJSON($result->any);
    }
    public function previewDocument($form) {
        $myParams = $this->params;
        $myParams->iFormID = (int)$form;
  	    $avail_list = $this->client->SiteLinkeSignPreviewDocumentURL( $myParams );
  	    $result = $avail_list->SiteLinkeSignPreviewDocumentURLResult;
  	    return $this->getJSON($result->any);
    }
	//Reporting API
    public function getManagementSummary($sD,$eD) {
        $params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->ManagementSummary($params);
		$result = $move_ins->ManagementSummaryResult;
		return $this->getJSON($result->any);
    }
    public function getBadDebts($sD,$eD) {
        $params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->BadDebts($params);
		$result = $move_ins->BadDebtsResult;
		return $this->getJSON($result->any);
    }
    public function doMoveOut($tenant_id, $unit_id) {
        $params = $this->params;
		$params->TenantID = (int)$tenant_id;
		$params->UnitID = (int)$unit_id;
        $params->sUsagePassword = 'MoveOut7243783';
		$move_ins = $this->client->MoveOut($params);
		$result = $move_ins->MoveOutResult;
		return $this->getJSON($result->any);
    }
    public function getInsuranceActivity($sD,$eD) {
        $params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->InsuranceActivity($params);
		$result = $move_ins->InsuranceActivityResult;
		return $this->getJSON($result->any);
    }
    public function getInsuranceStatement($sD,$eD) {
        $params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->InsuranceStatement($params);
		$result = $move_ins->InsuranceStatementResult;
		return $this->getJSON($result->any);
    }
    public function getMerchandiseSummary($sD,$eD) {
        $params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->MerchandiseSummary($params);
		$result = $move_ins->MerchandiseSummaryResult;
		return $this->getJSON($result->any);
    }
    public function getMarketingSummary($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->MarketingSummary($params);
		$result = $move_ins->MarketingSummaryResult;
		return $this->getJSON($result->any);
	}
    public function getReportingDiscounts($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->Discounts($params);
		$result = $move_ins->DiscountsResult;
		return $this->getJSON($result->any);
	}
    public function getManagementHistory($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->ManagementHistory($params);
		$result = $move_ins->ManagementHistoryResult;
		return $this->getJSON($result->any);
	}
    public function getDailyDeposits($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->DailyDeposits($params);
		$result = $move_ins->DailyDepositsResult;
		return $this->getJSON($result->any);
	}
    public function getPastDue($sD,$eD) {
        $params = $this->params;
  		$params->dReportDateStart = $sD;
  		$params->dReportDateEnd = $eD;
  		$move_ins = $this->client->PastDueBalances($params);
  		$result = $move_ins->PastDueBalancesResult;
  		return $this->getJSON($result->any);
    }
    public function getUnitPriceList() {
        $params = $this->params;
  		$move_ins = $this->client->UnitPriceList($params);
  		$result = $move_ins->UnitPriceListResult;
  		return $this->getJSON($result->any);
    }
	public function getMoveIns($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->MoveInsAndMoveOuts($params);
		$result = $move_ins->MoveInsAndMoveOutsResult;
		return $this->getJSON($result->any);
	}
	public function getMoveInsArray($sD,$eD) {
		$ret = $this->getMoveIns($sD,$eD);
		return ['moveIns' => $ret];
	}
    public function getInquiryTracking($sD,$eD) {
        $params = $this->params;
  		$params->dReportDateStart = $sD;
  		$params->dReportDateEnd = $eD;
  		$move_ins = $this->client->InquiryTracking($params);
  		$result = $move_ins->InquiryTrackingResult;
  		return $this->getJSON($result->any);
    }
    public function getReceipts($sD,$eD) {
        $params = $this->params;
  		$params->dReportDateStart = $sD;
  		$params->dReportDateEnd = $eD;
  		$move_ins = $this->client->Receipts($params);
  		$result = $move_ins->ReceiptsResult;
  		return $this->getJSON($result->any);
    }
    public function getAccountsReceivable($sD,$eD) {
        $params = $this->params;
  		$params->dReportDateStart = $sD;
  		$params->dReportDateEnd = $eD;
  		$move_ins = $this->client->AccountsReceivable($params);
  		$result = $move_ins->AccountsReceivableResult;
  		return $this->getJSON($result->any);
    }
    public function getConsolidatedSummary($sD,$eD) {
        $params = $this->params;
  		$params->dReportDateStart = $sD;
  		$params->dReportDateEnd = $eD;
  		$move_ins = $this->client->ConsolidatedManagementSummary($params);
  		$result = $move_ins->ConsolidatedManagementSummaryResult;
  		return $this->getJSON($result->any);
    }
    public function getFinancialSummary($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->FinancialSummary($params);
		$result = $move_ins->FinancialSummaryResult;
		return $this->getJSON($result->any);
	}
    public function getJournalEntries($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->GeneralJournalEntries($params);
		$result = $move_ins->GeneralJournalEntriesResult;
		return $this->getJSON($result->any);
	}
    public function getIncomeAnalysis($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->IncomeAnalysis($params);
		$result = $move_ins->IncomeAnalysisResult;
		return $this->getJSON($result->any);
	}
    public function insurancePolicyUpdate($unitName,$policy) {
		$params = $this->params;
		$params->sUnitName = $unitName;
		$params->sPolicyNum = $policy;
		$move_ins = $this->client->IncomeAnalysis($params);
		$result = $move_ins->IncomeAnalysisResult;
		return $this->getJSON($result->any);
	}
    public function getInsuranceRoll($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->InsuranceRoll($params);
		$result = $move_ins->InsuranceRollResult;
		return $this->getJSON($result->any);
	}
    public function getInsuranceSummary($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->InsuranceSummary($params);
		$result = $move_ins->InsuranceSummaryResult;
		return $this->getJSON($result->any);
	}
    public function getMarketingRoll($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->MarketingRoll($params);
		$result = $move_ins->MarketingRollResult;
		return $this->getJSON($result->any);
	}
    public function getMerchandiseActivity($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->MerchandiseActivity($params);
		$result = $move_ins->MerchandiseActivityResult;
		return $this->getJSON($result->any);
	}
    public function getRentRoll($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->RentRoll($params);
		$result = $move_ins->RentRollResult;
		return $this->getJSON($result->any);
	}
    public function getRentalActivity($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->RentalActivity($params);
		$result = $move_ins->RentalActivityResult;
		return $this->getJSON($result->any);
	}
    public function getScheduledAuctions() {
		$params = $this->params;
		$move_ins = $this->client->ScheduledAuctions($params);
		$result = $move_ins->ScheduledAuctionsResult;
		return $this->getJSON($result->any);
	}
    public function getScheduledMoveOuts() {
		$params = $this->params;
		$move_ins = $this->client->ScheduledMoveOuts($params);
		$result = $move_ins->ScheduledMoveOutsResult;
		return $this->getJSON($result->any);
	}
    public function getDispatchSched($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->MobDispatchSched($params);
		$result = $move_ins->MobDispatchSchedResult;
		return $this->getJSON($result->any);
	}
	public function getStatistics($sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->OccupancyStatistics($params);
		$result = $move_ins->OccupancyStatisticsResult;
		return $this->getJSON($result->any);
	}
    private function processTenants( $fac, $tenants, $prop_id = 0 ) {
        $ids = [];
        if ( sizeof( $tenants ) > 0 ) {
            foreach ($tenants as $tenant) {
                $model = new CandeePropertiesTenants();
                $model->tenant_id = $tenant['TenantID'];
                $model->email = $tenant['sEmail'];
                $model->first_name = $tenant['First_Name'];
                $model->last_name = $tenant['Last_Name'];
                $model->facility_id = $fac->facility_id;
                $model->property_id = $prop_id;
                $model->address = $tenant['address1'] ?? '';
                $model->city = $tenant['sCity'] ?? '';
                $model->region = $tenant['sRegion'] ?? '';
                $model->postal_code = $tenant['sPostalCode'] ?? '';
                $model->phone = str_replace(['+', '(', ')', '-', ' '], '', $tenant['Phone']) ?? '';
                $model->mobile = str_replace(['+', '(', ')', '-', ' '], '', $tenant['Mobile']) ?? '';
                $model->email_alt = ' ';
                $model->phone_alt = str_replace(['+', '(', ')', '-', ' '], '', $tenant['PhoneAlt']) ?? '';
                $model->gate_code = $tenant['GateCode'];
                $model->save();
                $ids[] = ($model->hasErrors()) ? $model->getErrors() : $model->id;
            }
        }
        return $ids;
    }
    private function processTenantReturn( $result, $facility_id, $prop_id = 0 ) {
        $tenants = [];
        if (sizeof($result['NewDataSet']['Table']) > 0) {
            foreach ($result['NewDataSet']['Table'] as $tenant) {
                $sEmail = (isset($tenant['sEmail']) && $tenant['sEmail'] != []) ? $tenant['sEmail'] : '';
                if ($sEmail != '') {
                    $tenants[] = [
                        'TenantID' => $tenant['TenantID'],
                        'GateCode' => $tenant['sAccessCode'],
                        'First_Name' => ($tenant['sFName'] != []) ? $tenant['sFName'] : '',
                        'Last_Name' => ($tenant['sLName'] != []) ? $tenant['sLName'] : '',
                        'Phone' => ($tenant['sPhone'] != []) ? str_replace(['+', '(', ')', '-', ' '], '', $tenant['sPhone']) : '',
                        'PhoneAlt' => ($tenant['sPhoneAlt'] != []) ? str_replace(['+', '(', ')', '-', ' '], '', $tenant['sPhoneAlt']) : '',
                        'sEmail' => ($tenant['sEmail'] != []) ? $tenant['sEmail'] : '',
                        'Mobile' => (isset($tenant['sMobile']) && $tenant['sMobile'] != []) ? str_replace(['+', '(', ')', '-', ' '], '', $tenant['sMobile']) : '',
                        'sCity' => (isset($tenant['sCity']) && $tenant['sCity'] != []) ? $tenant['sCity'] : '',
                        'sRegion' => (isset($tenant['sRegion']) && $tenant['sRegion'] != []) ? $tenant['sRegion'] : '',
                        'sPostalCode' => (isset($tenant['sPostalCode']) && $tenant['sPostalCode'] != []) ? $tenant['sPostalCode'] : '',
                        'facility_id' => (int)$facility_id,
                        'property_id' => (int)$prop_id ?? '',
                        'address1' => ($tenant['sAddr1'] != []) ? $tenant['sAddr1'] : '',
                        'address2' => ($tenant['sAddr2'] != []) ? $tenant['sAddr2'] : '',
                    ];
                }
            }
        }
        return $tenants;
    }
    private function processLedgersReturn( $data, $facility_id, $prop_id = 0 ) {
        $ledgers = [];
        if (!isset($data['NewDataSet']['Ledgers'][0])) {
            $data['NewDataSet']['Ledgers'] = [$data['NewDataSet']['Ledgers']];
        }
        if (sizeof($data['NewDataSet']['Ledgers']) > 0) {
            foreach ($data['NewDataSet']['Ledgers'] as $ledger) {
                $ledgers[] = [
                    'facility_id' => $facility_id,
                    'property_id' => $prop_id,
                    'tenant_id' => $ledger['TenantID'],
                    'unit_id' => $ledger['UnitID'],
                    'ledger_id' => $ledger['LedgerID'],
                    'first_name' => ($ledger['sFName'] != []) ? $ledger['sFName'] : '',
                    'last_name' => ($ledger['sLName'] != []) ? $ledger['sLName'] : '',
                    'balance' => ($ledger['dcChargeBalance'] != []) ? $ledger['dcChargeBalance'] : '',
                    'paid_through' => ($ledger['dPaidThru'] != []) ? date('Y-m-d', strtotime($ledger['dPaidThru'])) : '',
                    'autobill' => ($ledger['iAutoBillType'] != []) ? $ledger['iAutoBillType'] : 0,
                ];
            }
        }
        return $ledgers;
    }
    private function processLedger( $ledgers, $fac_id, $prop_id ) {
        $ids = [];
        if ( sizeof ( $ledgers ) > 0 ) {
            foreach ( $ledgers as $ledger ) {
                if ($prop_id != '' && $ledger['tenant_id'] != '') {
                    $model = new CandeePropertiesLedgers();
                    $model->setAttributes( $ledger );
                    $model->save();
                    $ids[] = ($model->hasErrors()) ? $model->getErrors() : $model->id;
                } else {
                    $ids[] = 'No Property ID or Tenant ID';
                }
            }
        }
        return $ids;
    }
    public function getLedgers( $tenants ) {
        $ledgers = [];

        $fac = Facilities::find()
            ->select(['facility_id', 'facility_name'])
            ->where(['facility_id' => $this->FAC_ID])
            ->one();

        if ($fac != '') {
            $prop = CandeeProperties::find()
                ->where(['cas_v2_fac_id' => $fac->facility_id])
                ->one();
            $prop_id = (string)($prop != '') ? $prop->id : '';

            $ledgerList = CandeePropertiesLedgers::find()
                ->where(['property_id' => $prop_id])
                ->andWhere(['tenant_id' => $tenants])
                ->all();
            if ( sizeof( $ledgerList ) > 0 ) {
                $test = date('Y-m-d H:i:s', strtotime('-30 minutes'));
			    $start = new \DateTime($ledgerList[0]->last_updated);
			    $since_start = $start->diff(new \DateTime());
			    $minutes = $since_start->i;

                if ($minutes > 30) {
                    CandeePropertiesLedgers::deleteAll(['property_id' => $prop_id]);
                    $myParams = $this->params;
                    $myParams->sTenantID = $tenants;
                    $avail_list = $this->client->LedgersByTenantID( $this->params );
              	    $result = $avail_list->LedgersByTenantIDResult;
                    $result = $this->getJSON( $result->any );
                    $ledgers = $this->processLedgersReturn( $result, $fac->facility_id, $prop_id );
                    $ids = $this->processLedger( $ledgers, $fac->facility_id, $prop_id );
                } else {
                    if ( sizeof( $ledgerList ) > 0 ) {
                        foreach ( $ledgerList as $ledger ) {
                            $ledgers[] = [
                                'facility_id' => $ledger['facility_id'],
                                'property_id' => $ledger['property_id'],
                                'tenant_id' => $ledger['tenant_id'],
                                'unit_id' => $ledger['unit_id'],
                                'ledger_id' => $ledger['ledger_id'],
                                'first_name' => $ledger['first_name'],
                                'last_name' => $ledger['last_name'],
                                'balance' => $ledger['balance'],
                                'paid_through' => date('Y-m-d', strtotime($ledger['paid_through'])),
                                'autobill' => $ledger['autobill'],
                            ];
                        }
                    }

                }
            } else {
                $myParams = $this->params;
                $myParams->sTenantID = $tenants;
                $avail_list = $this->client->LedgersByTenantID( $this->params );
          	    $result = $avail_list->LedgersByTenantIDResult;
                $result = $this->getJSON( $result->any );
                $ledgers = $this->processLedgersReturn( $result, $fac->facility_id, $prop_id );
                $ids = $this->processLedger( $ledgers, $fac->facility_id, $prop_id );
            }
        }
        return $ledgers;
    }
    public function getTenantListComplete( $debug = 0 ) {
        $tenants = $tenantList = $ids = [];

        $loc_code = str_replace($this->apiKey, '', $this->CORP_LOGIN);

        $fac = Facilities::find()
            ->select(['facility_id', 'facility_name'])
            ->where(['facility_id' => $this->FAC_ID])
            ->one();

        if ($fac != '') {
            $prop = CandeeProperties::find()
                ->where(['cas_v2_fac_id' => $fac->facility_id])
                ->one();
            $prop_id = ($prop != '') ? $prop->id : '';
            //echo 'Facility Found';
            $tenantList = CandeePropertiesTenants::find()
                ->where(['facility_id' => $fac->facility_id])
                ->orderBy(['last_updated' => SORT_DESC])
                ->all();
            //print_r($tenantList);
            if ( sizeof ( $tenantList ) > 0 ) {
                //echo 'Tenants Found in DB';
                $test = date('Y-m-d H:i:s', strtotime('-30 minutes'));
			    $start = new \DateTime($tenantList[0]->last_updated);
			    $since_start = $start->diff(new \DateTime());
			    $minutes = $since_start->i;

                if ($debug == 2) {
                    $params = $this->params;
                    $move_ins = $this->client->TenantListComplete ($params );
                    $result = $move_ins->TenantListCompleteResult;
                    $result = $this->getJSON( $result->any );
                    echo '<pre>';
                    print_r($result);
                    echo '</pre>';
                    exit();
                }

                if ($minutes > 20) {
                    //echo 'Cache is Old';
                    CandeePropertiesTenants::deleteAll(['facility_id' => $fac->facility_id]);
                    $params = $this->params;
                    $move_ins = $this->client->TenantListComplete ($params );
                    $result = $move_ins->TenantListCompleteResult;
                    $result = $this->getJSON( $result->any );
                    $tenants = $this->processTenantReturn( $result, $fac->facility_id, $prop_id );
                    $ids = $this->processTenants( $fac, $tenants, $prop_id );
                } else {
                    //echo 'Cache is Good';
                    foreach ( $tenantList as $tenant ) {
                        $tenants[] = [
                            'TenantID' => $tenant['tenant_id'],
                            'GateCode' => $tenant['gate_code'],
                            'First_Name' => $tenant['first_name'],
                            'Last_Name' => $tenant['last_name'],
                            'Phone' => $tenant['phone'],
                            'PhoneAlt' => $tenant['phone_alt'],
                            'sEmail' => $tenant['email'],
                            'Mobile' => $tenant['mobile'],
                            'sCity' => $tenant['city'],
                            'sRegion' => $tenant['region'],
                            'sPostalCode' => $tenant['postal_code'],
                            'facility_id' => $tenant['facility_id'],
                            'property_id' => $tenant['property_id']
                        ];
                        $ids[] = $tenant['id'];
                    }
                }
            } else {
                //echo 'No Tenants Found in DB';
                CandeePropertiesTenants::deleteAll(['facility_id' => $fac->facility_id]);
                $params = $this->params;
                $move_ins = $this->client->TenantListComplete( $params );
                $result = $move_ins->TenantListCompleteResult;
                $result = $this->getJSON( $result->any );
                $tenants = $this->processTenantReturn( $result, $fac->facility_id, $prop_id );
                $ids = $this->processTenants( $fac, $tenants, $prop_id );
            }
        }
        return $tenants;
	}
    public function getTenantListComplete_v2() {
		$params = $this->params;
        $date = getdate(time());
        $date_str = $date["mday"] . " " . $date["month"] . " " . $date["year"];
        $params->lngLastTimePolled = (string)strtotime( '-1 month', strtotime($date_str));
		$move_ins = $this->client->TenantListComplete_v2($params);
		$result = $move_ins->TenantListComplete_v2Result;
		$result = $this->getJSON($result->any);
	}
    public function getTenantRentChangeHistory($eD) {
		$params = $this->params;
		$params->dReportDateEnd = $eD;
		$move_ins = $this->client->TenantRentChangeHistory($params);
		$result = $move_ins->TenantRentChangeHistoryResult;
		return $this->getJSON($result->any);
	}
    public function getUnitPayments($unit,$sD,$eD) {
		$params = $this->params;
		$params->dReportDateStart = $sD;
		$params->dReportDateEnd = $eD;
        $params->sUnitName = $unit;
		$move_ins = $this->client->UnitPayments($params);
		$result = $move_ins->UnitPaymentsResult;
		return $this->getJSON($result->any);
	}
    public function getTenantNotes($ledger) {
        $params = $this->params;
        $params->iLedgerID = (int)$ledger;
		$move_ins = $this->client->TenantNotesRetrieve($params);
		$result = $move_ins->TenantNotesRetrieveResult;
		return $this->getJSON($result->any);
    }
    public function insertNote($ledger, $note) {
        $params = $this->params;
        $params->iLedgerID = (int)$ledger;
        $params->sNote = $note;
		$move_ins = $this->client->TenantNoteInsert($params);
		$result = $move_ins->TenantNoteInsertResult;
		return $this->getJSON($result->any);
    }
    public function insertNote_v2($ledger, $note, $type) {
        $params = $this->params;
        $params->iLedgerID = (int)$ledger;
        $params->sNote = $note;
        $params->iNoteType = (int)$type;
        $move_ins = $this->client->TenantNoteInsert_v2($params);
        $result = $move_ins->TenantNoteInsert_v2Result;
        return $this->getJSON($result->any);
    }
    public function noteTypes() {
        return $this->getJSON([
            'Note', 'Letter', 'Email', 'TenantContents', 'Autobill',
            'Discount or Credit Explanation', 'Non-Letter Print',
            'Non-Letter Email', 'Non-Collection Call', 'Mail Export',
            'Non-Letter Mail Export', 'SMS', 'Non-Letter SMS',
            'Credit Card', 'ACH'
        ]);
    }
    public function eSignRetrieve($tenant) {
        $params = $this->params;
        $params->iTenantID = (int)$tenant;
		$move_ins = $this->client->SiteLinkeSignAndeFilesRetrieve($params);
		$result = $move_ins->SiteLinkeSignAndeFilesRetrieveResult;
		return $this->getJSON($result->any);
    }
    public function addUnit(
        $name, $type_id, $width, $length, $std_rate, $std_weekly_rate,
        $std_deposit, $std_late_fee, $floor, $power, $climate, $inside,
        $alarm, $collapsible, $mobile, $corporate, $entry, $door, $rentable,
        $ada, $exclude
    ) {
        $params = $this->params;
        $params->sUnitName = $name;
        $params->UnitTypeID = (int)$type_id;
        $params->dcWidth = (double)$width;
        $params->dcLength = (double)$length;
        $params->dcStdRate = (double)$std_rate;
        $params->dcStdWeeklyRate = (double)$std_weekly_rate;
        $params->dcStdSecDep = (double)$std_deposit;
        $params->dcStdLateFee = (double)$std_late_fee;
        $params->iFloor = (int)$floor;
        $params->bPower = (bool)$power;
        $params->bClimate = (bool)$climate;
        $params->bInside = (bool)$inside;
        $params->bAlarm = (bool)$alarm;
        $params->bCollapsible = (bool)$collapsible;
        $params->bMobile = (bool)$mobile;
        $params->bCorporate = (bool)$corporate;
        $params->iEntryLoc = (int)$entry;
        $params->iDoorType = (int)$door;
        $params->bRentable = (bool)$rentable;
        $params->iADA = (int)$ada;
        $params->bExcludeFromWebsite = (bool)$exclude;
        $move_ins = $this->client->UnitAdd($params);
		$result = $move_ins->UnitAddResult;
		return $this->getJSON($result->any);
    }
    public function updateTenantPassword($tenant, $email, $pass, $question, $answer) {
        $params = $this->params;
        $params->TenantID = (int)$tenant;
        $params->sEmail = $email;
        $params->sWebPassword = $pass;
        $params->sWebSecurityQ = $question;
        $params->sWebSecurityQA = $answer;
        $move_ins = $this->client->TenantLoginAndSecurityUpdate($params);
		$result = $move_ins->TenantLoginAndSecurityUpdateResult;
		return $this->getJSON($result->any);
    }
    public function modifyTenant($tenant, $first, $last, $gate, $add, $city, $state,
        $zip, $phone, $mobile, $email, $commercial, $companyTenant, $dob
    ) {
        $params = $this->params;
        $params->iTenantID = (int)$tenant;
        $params->sGateCode = $gate;
        $params->sFName = $first;
        $params->sLName = $last;
        $params->sAddr1 = $add;
        $params->sCity = $city;
        $params->sRegion = $state;
        $params->sPostalCode = $zip;
        $params->sPhone = $phone;
        $params->sMobile = $mobile;
        $params->sEmail = $email;
        $params->bCommercial = (bool)$commercial;
        $params->bCompanyIsTenant = false;
        $params->dDOB = $dob;
        $move_ins = $this->client->TenantUpdate($params);
		$result = $move_ins->TenantUpdateResult;
		return $this->getJSON($result->any);
    }
    public function updateTenant(
        $tenant, $gate_code, $pass, $mr_mrs, $first, $mid, $last, $company,
        $add1, $add2, $city, $region, $postal, $country, $phone, $mr_mrs_alt,
        $first_alt, $mid_alt, $last_alt
    ) {
        $params = $this->params;
        $params->iTenantID = (int)$tenant;
        $params->sGateCode = $gate_code;
        $params->sWebPassword = $pass;
        $params->sMrMrs = $mr_mrs;
        $params->sFName = $first;
        $params->sMI = $mid;
        $params->sLName = $last;
        $params->sCompany = $company;
        $params->sAddr1 = $add1;
        $params->sAddr2 = $add2;
        $params->sCity = $city;
        $params->sRegion = $region;
        $params->sPostalCode = $postal;
        $params->sCountry = $country;
        $params->sPhone = $phone;
        $params->sMrMrsAlt = $mr_mrs_alt;
        $params->sFNameAlt = $first_alt;
        $params->sMIAlt = $mid_alt;
        $params->sLNameAlt = $last_alt;
        $params->sAddr1Alt = '';
        $params->sAddr2Alt = '';
        $params->sCityAlt = '';
        $params->sRegionAlt = '';
        $params->sPostalCodeAlt = '';
        $params->sCountryAlt = '';
        $params->sPhoneAlt = '';
        $params->sMrMrsBus = '';
        $params->sFNameBus = '';
        $params->sMIBus = '';
        $params->sLNameBus = '';
        $params->sCompanyBus = '';
        $params->sAddr1Bus = '';
        $params->sAddr2Bus = '';
        $params->sCityBus = '';
        $params->sRegionBus = '';
        $params->sPostalCodeBus = '';
        $params->sCountryBus = '';
        $params->sPhoneBus = '';
        $params->sFax = '';
        $params->sEmail = '';
        $params->sPager = '';
        $params->sMobile = '';
        $params->bCommercial  = false;
        $params->bCompanyIsTenant  = false;
        $params->dDOB = '';
        $params->sTenNote = '';
        $params->sLicense = '';
        $params->sLicRegion = '';
        $params->sSSN = '';
        $params->sEmailAlt = '';
        $params->sRelationshipAlt = '';
        $move_ins = $this->client->TenantUpdate($params);
		$result = $move_ins->TenantUpdateResult;
		return $this->getJSON($result->any);
    }
    public function refundCC($tenant, $unit, $reason, $type, $ccNo, $cvv, $exp, $name, $add, $zip) {
        $params = $this->params;
        $params->tenantId = (int)$tenant;
        $params->unitId = (int)$unit;
        $params->sReason = $reason;
        $params->iCreditCardType = (int)$type;
        $params->sCreditCardNumber = $ccNo;
        $params->sCreditCardCVV = $cvv;
        $params->dExpirationDate = $exp;
        $params->sBillingName = $name;
        $params->sBillingAddress = $add;
        $params->sBillingZipCode = $zip;
        $move_ins = $this->client->RefundPaymentCreditCard($params);
		$result = $move_ins->RefundPaymentCreditCardResult;
		return $this->getJSON($result->any);
    }
    public function simplePayment(
        $tenant, $unit, $amount, $type, $number, $cvv, $exp, $name, $add, $zip, $test = true
    ) {
        $params = $this->params;
        $params->iTenantID = (int)$tenant;
        $params->iUnitID = (int)$unit;
        $params->dcPaymentAmount = (double)$amount;
        $params->iCreditCardType = (int)$type;
        $params->sCreditCardNumber = $number;
        $params->sCreditCardCVV = $cvv;
        $params->dExpirationDate = date('c', strtotime($exp));
        $params->sBillingName = $name;
        $params->sBillingAddress = $add;
        $params->sBillingZipCode = $zip;
        $params->bTestMode = (bool)$test;
        $move_ins = $this->client->PaymentSimple($params);
		$result = $move_ins->PaymentSimpleResult;
		return $this->getJSON($result->any);
    }

    public function TenantImageUpload($tenantID,$imageBytes,$filename) {

        $params = $this->params;
        $params->iTenantID = (int)$tenantID;
        $params->aryImageBytes = $imageBytes;
        $params->sFileName = $filename;
        $move_ins = $this->client->TenantImageUpload($params);
        $result = $move_ins->TenantImageUploadResult;
        return $this->getJSON($result->any);
    }
  }
