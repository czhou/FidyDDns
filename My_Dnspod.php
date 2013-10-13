<?php
class My_Dnspod {
	/**
	 * 
	 * @var Zend_Http_Client
	 */
	protected $client = null;
	protected $dnspodApi = "https://dnsapi.cn/";
	protected $login_email = "youremail@xxx.com";//Dnspod的登陆用户名
	protected $login_password = "******";//登录密码
	protected $format = "json";
	public $my_ip = null;
	protected $domain = null;
	protected $UA = "FIDY DDNS Client/1.0.0 (fidy@fidy.net)"; //Dnspod要求的格式，改成你自己的电子邮件地址。
	
	
	public function __construct(){
		$this->client = new Zend_Http_Client(null, array("keepalive"=>true));
		$this->client->setCookieJar(true);
		$this->my_ip = trim(strip_tags(file_get_contents("http://www.fidy.net/ip.php")));//用来获取机器的ip地址，最好替换成你自己的。
		
	}
	
	
	public function getDomainInfo($domainName){
		$this->client->resetParameters(true);
		$this->client->setHeaders("User-Agent", $this->UA);
		$this->client->setUri($this->dnspodApi . "Domain.List");
		$this->client->setParameterPost("login_email", $this->login_email);
		$this->client->setParameterPost("login_password", $this->login_password);
		$this->client->setParameterPost("format", $this->format);
		$this->client->setParameterPost("type", "mine");
		$response = $this->client->request("POST");
		
		$result = Zend_Json::decode($response->getBody());
		
		if ($result["status"]["code"] == 1) {
			foreach ($result["domains"] as $domain){
				if ($domain["name"] == $domainName) {
					$domainID = $domain["id"];
					$this->domain = $this->getSubdomainList($domainID);
					return $this->domain;
				}
			}
		}
		
		return false;
		
	}
	
	private function getSubdomainList($domainID){
		$this->client->resetParameters(true);
		$this->client->setHeaders("User-Agent", $this->UA);
		$this->client->setUri($this->dnspodApi . "Record.List");
		$this->client->setParameterPost("login_email", $this->login_email);
		$this->client->setParameterPost("login_password", $this->login_password);
		$this->client->setParameterPost("format", $this->format);
		$this->client->setParameterPost("domain_id", $domainID);
		$response = $this->client->request("POST");
		$result = Zend_Json::decode($response->getBody());
		
		return $result;
	}
	
	public function setSubdomainIP($subdomainName, $ip){
		$domainID = $this->domain["domain"]["id"];
		$recordID = null;
		foreach ($this->domain["records"] as $record){
			if ($subdomainName == $record["name"]) {
				if ($record["value"] == $ip) {
					return false;
				}
				$recordID = $record["id"];
			}
		}
		
		if ($recordID == null) {
			return false;
		}
		
		
		$this->client->resetParameters(true);
		$this->client->setHeaders("User-Agent", $this->UA);
		$this->client->setUri($this->dnspodApi . "Record.Modify");
		$this->client->setParameterPost("login_email", $this->login_email);
		$this->client->setParameterPost("login_password", $this->login_password);
		$this->client->setParameterPost("format", $this->format);
		$this->client->setParameterPost("domain_id", $this->domain["domain"]["id"]);
		$this->client->setParameterPost("record_id", $recordID);
		$this->client->setParameterPost("record_type", "A");
		$this->client->setParameterPost("record_line", "默认");
		$this->client->setParameterPost("sub_domain", $subdomainName);
		$this->client->setParameterPost("value", $ip);
		$response = $this->client->request("POST");
		$result = Zend_Json::decode($response->getBody());
		
		return $result;
	}
	
	
}
