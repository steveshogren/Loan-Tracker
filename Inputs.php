<?php
class Inputs
{

    public function handleInputs($post)
    {
        $AccountData = $this->_buildAccountData($post);
        $accountNeedsToBeCreated = (
            $AccountData->password2
            && $AccountData instanceof Account_Data
        );
        $Authenticator = new Authenticator();

        if ($accountNeedsToBeCreated) {
            $message = $Authenticator->createAccount($AccountData);
            return $message;
        } else {
            if ($AccountData instanceof Account_Data) {
                $result = $Authenticator->authenticateAccount($AccountData);
            }
        }
    }

    /**
     * @param $post
     * @return Account_Data
     */
    public function buildAccountData($post)
    {
        $username = $post['username'];
        if (
            isset($username)
            && $username != ''
            && isset($post['password'])
            && $post['password'] != ''
        ) {
            $AccountData = new Account_Data();
            $AccountData->username = $username;
            $AccountData->password = $post['password'];
            if (isset($post['password2'])) {
                $AccountData->password2 = $post['password2'];
            }

            return $AccountData;
        }
    }
}

