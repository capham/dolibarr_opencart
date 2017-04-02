<?php
class ActionsOpencart
{
    function emailElementlist($parameters, &$object, &$action, $hookmanager)
    {
        if (in_array('admin', explode(':', $parameters['context'])))
        {
            $emailTemplateType = [
                'oc_new_order_send' => 'To send mail new order in Opencart',
                'oc_approved_order_send' => 'To send mail appreved order in Opencart',
                'oc_new_project_send' => 'To send mail new project in Opencart',
            ];
            $this->results = $emailTemplateType;
            return 1;
        }
        return -1;
    }
}
?>