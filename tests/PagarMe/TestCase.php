<?php

abstract class PagarMeTestCase extends UnitTestCase {
	

	protected static function setAntiFraud($status) {
		// authorizeFromEnv();	
		// $request = new PagarMe_Request('/company', 'PUT');
		// $request->setParameters(array('antifraud' => $status));	
		// $response = $request->run();
	}

	protected static function createTestTransaction(array $attributes = array()) 
	{
		authorizeFromEnv();	
		return new PagarMe_Transaction(
			$attributes + 
			array(
			"amount" => 'R$ 10,00',
			"card_number" => "4901720080344448",
			"card_holder_name" => "Jose da Silva",
			"card_expiration_month" => 12,
			"card_expiration_year" => 15,
			"card_cvv" => "123",
		));
	}

	protected static function createTestTransactionWithCustomer(array $attributes = array()) {
		authorizeFromEnv();	
		$customer = array('customer' => array(
				'name' => "Jose da Silva",  
				'document_number' => "36433809847", 
				'email' => "henrique@pagar.me", 
				'address' => array(
					'street' => "Av Faria Lima",
					'neighborhood' => 'Jardim Europa',
					'zipcode' => '12460000', 
					'street_number' => 296, 
					'complementary' => '8 andar'
				),
				'phone' => array(
					'type' => "cellphone",
					'ddd' => 12, 
					'number' => '981433533', 
				),
				'sex' => 'M', 
				'born_at' => '1995-10-11'));
		return self::createTestTransaction($customer);
	}

	protected static function createTestPlan(array $attributes = array()) {
		authorizeFromEnv();		
		return new PagarMe_Plan($attributes +
			array(
				'amount' => 1000,
				'days' => '30',
				'name' => "Plano Silver",
				'trial_days' => '2'	
			)
		);
	}	

	protected static function createTestSubscription(array $attributes = array()) {
		authorizeFromEnv();	
		return new PagarMe_Subscription($attributes + array(
			"amount" => 'R$ 10,00',
			"card_number" => "4901720080344448",
			"card_holder_name" => "Jose da Silva",
			"card_expiration_month" => 12,
			"card_expiration_year" => 15,
			"card_cvv" => "123",
			'customer' => array(
				'email' => 'customer@pagar.me'
			)
		));
	}

	protected function validateCustomerResponse($customer) {
		authorizeFromEnv();	
		$this->assertTrue($customer->getId());
		$this->assertTrue($customer->getAddresses());
		$this->assertTrue($customer->getPhones());
		$this->assertEqual($customer->getDocumentType(), 'cpf');
		$this->assertEqual($customer->getName(), 'Jose da Silva');
		$this->assertTrue($customer->getBornAt());
		$this->assertEqual($customer->getGender(), 'M');

		$addresses = $customer->getAddresses();
		$addr =  end($addresses);
		$this->assertEqual($addr->getStreet(), 'Av Faria Lima');
		$this->assertTrue($addr->getCity());
		$this->assertTrue($addr->getCountry());
		$this->assertEqual($addr->getNeighborhood(), 'Jardim Europa');
		$this->assertEqual($addr->getStreetNumber(), 296);
		$this->assertEqual($addr->getComplementary(), '8 andar');

		$phones = $customer->getPhones();
		$phone = $phones[0];
		$this->assertEqual($phone->getType(), 'cellphone');
		$this->assertEqual($phone->getDDD(), '12');
		$this->assertEqual($phone->getNumber(), '981433533');
	}

	protected function validateTransactionResponse($transaction) {
		authorizeFromEnv();	
		$this->assertTrue($transaction->getId());	
		$this->assertEqual($transaction->getCardHolderName(), 'Jose da Silva');
		$this->assertTrue($transaction->getDateCreated());
		$this->assertEqual($transaction->getAmount(), 1000);
		$this->assertEqual($transaction->getInstallments(), '1');
		// $this->assertEqual($transaction->getStatus(), 'paid');
		$this->assertFalse($transaction->getRefuseReason());
		$this->assertFalse($transaction->getBoletoBarcode());
		$this->assertFalse($transaction->getBoletoUrl());

		if($transaction->getCustomer()) {
			$customer = $transaction->getCustomer();
			$this->validateCustomerResponse($customer);	
		}
	}

}

?>
