<?php

namespace AppBundle\Globals;


use Symfony\Component\Config\Definition\Exception\Exception;

class Utils {

    const FULL_CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ?!=/%$.-_,#+@';
    const SMALL_CHARSET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function generateRandomString($length = 10, $characters = self::FULL_CHARSET) {
        $charactersLength = strlen($characters);
        $randomString = '';
        try {
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
        } catch(Exception $exception) {
            $randomString = 'EE';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        }

        return $randomString;
    }

    public static function generateUsername($length = 8) {
        return self::generateRandomString($length, self::SMALL_CHARSET);
    }

    public static function isValidMail($mail) {
        $user   = '[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+';
        $domain = '(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+';
        $ipv4   = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
        $ipv6   = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';

        return preg_match("/^$user@($domain|(\[($ipv4|$ipv6)\]))$/", $mail);
    }

}