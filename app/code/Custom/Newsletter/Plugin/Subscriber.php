<?php
namespace Custom\Newsletter\Plugin;

use Magento\Framework\App\Request\Http;

class Subscriber {
    protected $request;
    public function __construct(Http $request){
        $this->request = $request;
    }

    public function aroundSubscribe($subject, \Closure $proceed, $email) {
        $result = $proceed($email);
        if ($this->request->isPost() && $this->request->getPost('fullname')) { 

            $fullname = $this->request->getPost('fullname');

            $subject->setCFullname($fullname);

            try {
                $subject->save();
            }catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
        return $result;
    }
}