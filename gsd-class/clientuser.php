<?php

class clientuser extends user {
    
    public $company, $address, $country, $phone, $fax, $website, $facebook, $twitter, $linkedin;

    public function login ($email, $password) {
        $fields = array ('company', 'address', 'country', 'phone', 'fax', 'website', 'facebook', 'twitter', 'linkedin');

        $result = parent::login($email, $password, $fields);

        if (!empty($result)) {
            
            foreach ($fields as $field) {
                $this->{$field} = $result[$field];
            }
        }
        
        return !empty($result);
    }
}
