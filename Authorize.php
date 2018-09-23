<?php
/*

	Copyright 2018 Richard McQuiston
	Free for personal, non-commercial use.
	
	Donations Gladly Accepted via PayPal to bigrpromotions@gmail.com
*/

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\{AuthorizeLog};
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizeNet extends Model {
    public $name = '';
    public $transaction_key = '';
    public $authentication = '';

    public function authenticate() {
        if ($this->name != '' && $this->transaction_key != '') {
            $this->authentication = new AnetAPI\MerchantAuthenticationType();
            $this->authentication->setName($this->name);
            $this->authentication->setTransactionKey($this->transaction_key);
            return 1;
        }
        return -1;
    }
    public function subscription_detail_type($id,$name,$status,$timestamp,$first,$last,$total,$past,$payment_method,$account_no,$invoice,$amount,$currency,$profile_id,$shipping_profile) {
        if ($id != '' && $name != '') {
            $type = new AnetAPI\SubscriptionDetailType();
            $type->setId((int)$id);
            return $type;
        }
        return -1;
    }
    public function subscription_profile_type($pay_profile, $ship_profile) {
        if ($pay_profile != '' && $ship_profile != '') {
            $profile = new AnetAPI\SubscriptionCustomerProfileType();
            $profile->setPaymentProfile($pay_profile);
            $profile->setShippingProfile($ship_profile);
            return $profile;
        }
        return -1;
    }
    public function subscription_payment_type() {
        if ($id != '' && $no != '') {
            $sub = new AnetAPI\SubscriptionPaymentType();
            $sub->setId((int)$id);
            $sub->setPayNum((int)$no);
            return $sub;
        }
        return -1;
    }
    public function line_item($id,$name,$description,$quantity,$unit_price,$tax) {
        if ($id != '' && $name != '' && $description != '' && $quantity != '' && $unit_price != '') {
            $line_item = new AnetAPI\LineItemType();
             $line_item->setItemId($id);
             $line_item->setName($name);
             $line_item->setDescription($description);
             $line_item->setQuantity((int)$quantity);
             $line_item->setUnitPrice((double)$unit_price);
             $line_item->setTaxable((bool)$tax);
             return $line_item;
        }
        return -1;
    }
    public function credit_card($number, $date, $code) {
        if (strlen($number) >= 15 && strlen($date) == 7 && strlen($code) >= 3) {
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($number);
            $creditCard->setExpirationDate($date);
            $creditCard->setCardCode($code);
            return $creditCard;
        }
        return -1;
    }
    public function payment_type($type) {
        if ($type != '') {
            $pay_type = new AnetAPI\PaymentType();
            $pay_type->setCreditCard($type);
            return $pay_type;
        }
        return -1;
    }
    public function order_type($invoice,$description) {
        if ($invoice != '' && $description != '') {
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($invoice);
            $order->setDescription($description);
            return $order;
        }
        return -1;
    }
    public function customer_address($first,$last,$comp = '',$add,$city,$state,$zip,$country = '') {
        if ($first != '' && $last != '' && $add != '' && $city != '' && $state != '' && $zip != '') {
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($first);
            $customerAddress->setLastName($last);
            if ($comp != '') {
                $customerAddress->setCompany($comp);
            }
            $customerAddress->setAddress($add);
            $customerAddress->setCity($city);
            $customerAddress->setState($state);
            $customerAddress->setZip($zip);
            if ($country != '') {
                $customerAddress->setCountry($country);
            }
            return $customerAddress;
        }
        return -1;
    }
    public function customer_data_type($type, $id, $email) {
        if ($type != '' && $id != '' && $email != '') {
            $data = new AnetAPI\CustomerDataType();
            $data->setType($type);
            $data->setId($id);
            $data->setEmail($email);
            return $data;
        }
        return -1;
    }
    public function setting_type($name,$value) {
        if ($name != '' && $value != '') {
            $setting = new AnetAPI\SettingType();
            $setting->setSettingName($name);
            $setting->setSettingValue($value);
            return $setting;
        }
        return -1;
    }
    public function user_field_type($name,$value) {
        if ($name != '' && $value != '') {
            $merchantDefinedField = new AnetAPI\UserFieldType();
            $merchantDefinedField->setName($name);
            $merchantDefinedField->setValue($value);
            return $merchantDefinedField;
        }
        return -1;
    }
    public function transaction_request_type($type,$amount,$order,$payment,$billing_add,$customer_data,$settings = [], $fields = [], $line_items = []) {
        if ($type != '' && $amount != '' && $payment != '' && $billing_add != '' && $customer_data != '') {
            $request = new AnetAPI\TransactionRequestType();
            $request->setTransactionType($type);
            $request->setAmount((double)$amount);
            $request->setOrder($order);
            $request->setPayment($payment);
            $request->setBillTo($billing_add);
            $request->setCustomer($customer_data);
            if (sizeof($settings) > 0) {
                foreach ($settings as $s) {
                    $request->addToTransactionSettings($s);
                }
            }
            if (sizeof($fields) > 0) {
                foreach ($fields as $f) {
                    $request->addToUserFields($f);
                }
            }
            if (sizeof($line_items) > 0) {
                $request->setLineItems($line_items);
            }
            return $request;
        }
        return -1;
    }
    public function transaction_request($authentication, $ref_id, $request_type) {
        if ($authentication != '' && $ref_id != '' && $request_type != '') {
            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($authentication);
            $request->setRefId($ref_id);
            $request->setTransactionRequest($request_type);
            return $request;
        }
        return -1;
    }
    public function transaction_controller($request) {
        if ($request != '') {
            $controller = new AnetController\CreateTransactionController($request);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
            return $response;
        }
        return -1;
    }
    public function handle_response($response) {
        $errors = [];
        if ($response != null) {
            if ($response == -1) {
                $errors[] = 'transaction_controller -> Missing Fields';
            } else {
                if ($response->getMessages()->getResultCode() == 'Ok') {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $errors[] = " Successfully created transaction with Transaction ID: " . $tresponse->getTransId();
                        $errors[] = " Transaction Response Code: " . $tresponse->getResponseCode();
                        $errors[] = " Message Code: " . $tresponse->getMessages()[0]->getCode();
                        $errors[] = " Auth Code: " . $tresponse->getAuthCode();
                        $errors[] = " Description: " . $tresponse->getMessages()[0]->getDescription();
                    } else {
                        $errors[] = 'Transaction Failed';
                        if ($tresponse->getErrors() != null) {
                            $errors[] = $tresponse->getErrors()[0]->getErrorCode() . ' = ' . $tresponse->getErrors()[0]->getErrorText();
                        }
                    }
                } else {
                    echo "Transaction Failed \n";
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                        echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                    } else {
                        echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                        echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
                    }
                }
            }
        } else {
            $errors[] = 'transaction_controller -> No Response';
        }
        return $errors;
    }
    public function authorizeCardTest() {
        $ret = $errors =[];
        $ret['authenticate'] = $this->authenticate();
        $ref_id = 'ref' . time();
        $amount = '1.23';
        $cc = $this->credit_card('4111111111111111','2038-12','123');
            $ret['credit_card'] = $cc;
        $pt = $this->payment_type($cc);
            $ret['pay_type'] = $pt;
        $order = $this->order_type('30303', 'Basketball Shirts');
            $ret['order_type'] = $order;
        $cust_add = $this->customer_address('Dakota',"Johnson","Happyville","123 Easy Street","Almond Falls","GA","13131","USA");
            $ret['cust_address'] = $cust_add;
        $data_type = $this->customer_data_type("individual", "97799433654", "Dakota@example.com");
            $ret['data_type'] = $data_type;
        $duplicateWindowSetting = $this->setting_type('duplicateWindow','60');
            $ret['settings'][] = $duplicateWindowSetting;
        $field_1 = $this->user_field_type('customerLoyaltyNum', '2345');
            $ret['fields'][] = $field_1;
        $field_2 = $this->user_field_type('favorite_color', 'orange');
            $ret['fields'][] = $field_2;
        $request_type = $this->transaction_request_type('authOnlyTransaction',$amount,$order,$pt,$cust_add,$data_type,$ret['settings'], $ret['fields']);
            $ret['request_type'] = $request_type;
        $request = $this->transaction_request($this->authentication, $ref_id, $request_type);
            $ret['request'] = $request;
        $response = $this->transaction_controller($request);
        $errors[] = $this->handle_response($response);

        return json_encode([
            'responses' => $ret,
            'messages' => $errors,
        ]);
    }
}
