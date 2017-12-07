<?php
namespace PayPalBR\PayPalPlus\Api;

interface WebHookManagementInterface
{
        /**
         * POST for WebHook api
         * @param string $param
         * @return string
         */
        public function postWebHook($params);
            /**
         * GET for WebHook api
         * @param string $param
         * @return string
         */
        public function getWebHook($id);
            /**
         * GET for WebHook api
         * @param string $id
         * @return string
         */
        public function patchWebHook($params);

        /**
         * POST for WebHook api
         * @param string $param
         * @return string
         */
        public function deleteWebHook($id);
            /**
         * GET for WebHook api
         * @param string $id
         * @return string
         */
        public function getEvent($id);
            /**
         * POST for WebHook api
         * @param string $id
         * @return string
         */
        public function verifySignature($params);
            /**
         * POST for WebHook api
         * @param string $param
         * @return string
         */
        public function resendEvent($id);
        /**
        * GET for WebHook api
        * @param string $param
        * @return string
        */
        public function getEventByParams($params);
}
