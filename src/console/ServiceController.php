<?php
namespace yoo\console;

/**
 * 服务
 * ---
 * @author Verdient。
 */
class ServiceController extends Controller
{
	/**
	 * services()
	 * 服务配置
	 * ----------
	 * @return Array
	 * @author Verdient。
	 */
	public function services(){
		return [];
	}

	/**
	 * requestServices(Array $args, Boolean $exist = true)
	 * 获取请求的服务
	 * ---------------------------------------------------
	 * @param Array $args 请求的参数
	 * @param Boolean $onlyExist 是否只包含已存在的
	 * -----------------------------------------
	 * @return Array
	 * @author Verdient。
	 */
	public function requestServices($args, $exist = true){
		$services = array_keys($this->services());
		if(!empty($args)){
			$services = array_intersect($services, $args);
			if(empty($services)){
				$this->fatalError('未知的服务: ' . implode(', ', $args));
			}
		}
		if($exist === true){
			$services = array_intersect($services, $this->getRuningServices());
		}else{
			$services = array_diff($services, $this->getRuningServices());
		}
		return $services;
	}

	/**
	 * @var Boolean $_environmentOK
	 * 环境是否OK
	 * ----------------------------
	 * @author Verdient。
	 */
	protected $_environmentOK = false;

	/**
	 * runCommand(String $command)
	 * 执行命令
	 * ---------------------------
	 * @param String $command 命令
	 * --------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function runCommand($command){
		exec($command, $output);
		return $output;
	}

	/**
	 * runPM2Command(String $command)
	 * 运行PM2命令
	 * ------------------------------
	 * @param String $command 命令
	 * --------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function runPM2Command($command){
		$this->checkEnvironment();
		return $this->runCommand('pm2 ' . $command);
	}

	/**
	 * normalizeOutput(Array $output)
	 * 格式化输出
	 * ------------------------------
	 * @param Array $output 输出
	 * ------------------------
	 * @return Array
	 * @author Verdient。
	 */
	protected function normalizeOutput($output){
		foreach($output as &$row){
			$row = explode('│', $row);
			foreach($row as &$element){
				$element = trim($element);
			}
		}
		return $output;
	}

	/**
	 * getRuningServices()
	 * 获取正在运行的服务
	 * -------------------
	 * @author Verdient。
	 */
	protected function getRuningServices(){
		$services = [];
		$output = $this->list();
		$length = count($output);
		$nameIndex = false;
		if($length > 5){
			$output = $this->normalizeOutput($output);
			$tilte = $output[1];
			$nameIndex = array_search('App name', $tilte);
			if(!$nameIndex){
				$this->fatalError('无法解析名称');
			}
			for($i = 3; $i < ($length - 2); $i++){
				$services[] = $output[$i][$nameIndex];
			}
		}
		return $services;
	}

	/**
	 * checkNodeJs()
	 * 检查node.js
	 * -------------
	 * @return Boolean
	 * @author Verdient。
	 */
	public function checkNodeJs(){
		$result = $this->runCommand('node -v');
		if(preg_match('/^v\d+\.\d+\.\d+$/', $result[0]) === 0){
			$this->fatalError('请先安装node.js');
		}
	}

	/**
	 * checkPM2()
	 * 检查PM2
	 * ----------
	 * @author Verdient。
	 */
	public function checkPM2(){
		$result = $this->runCommand('pm2 -v');
		if(preg_match('/^\d+\.\d+\.\d+$/', $result[0]) === 0){
			$this->fatalError('请先安装PM2');
		}
	}

	/**
	 * checkEnvironment()
	 * 检查环境
	 * ------------------
	 * @author Verdient。
	 */
	public function checkEnvironment(){
		if($this->_environmentOK === false){
			$this->info('正在检查执行环境');
			$this->checkNodeJs();
			$this->checkPM2();
			$this->_environmentOK = true;
		}
	}

	/**
	 * 启动
	 * ----
	 * @author Verdient。
	 */
	public function actionStart(){
		$this->info('开始启动进程');
		$requestServices = $this->requestServices(func_get_args(), false);
		$services = $this->services();
		$count = count($requestServices);
		if($count > 0){
			$this->info('需启动' . $count . '个服务');
			$this->info('开始启动服务');
			$index = 1;
			foreach($requestServices as $name){
				$this->info('正在启动服务(' . $index . '/' . $count . ')');
				$command = 'start php --name "' . $name . '" -- ' . BASE_PATH . DIRECTORY_SEPARATOR . 'yii -- ' . $services[$name];
				$this->runPM2Command($command);
				$this->scrollDown();
				$index ++;
			}
			$this->info('服务启动完成，请核实各服务运行情况');
			$this->printList();
		}else{
			$this->error('没有需要启动的服务');
		}
	}

	/**
	 * 停止
	 * ---
	 * @author Verdient。
	 */
	public function actionStop(){
		$this->alter('stop', func_get_args());
	}

	/**
	 * 重启
	 * ----
	 * @author Verdient。
	 */
	public function actionRestart(){
		$this->alter('restart', func_get_args());
	}

	/**
	 * 重置
	 * ---
	 * @author Verdient。
	 */
	public function actionReset(){
		$this->alter('reset', func_get_args());
	}

	/**
	 * 删除
	 * ---
	 * @author Verdient。
	 */
	public function actionDelete(){
		$this->alter('delete', func_get_args());
	}

	/**
	 * 任务列表
	 * -------
	 * @author Verdient。
	 */
	public function actionList(){
		$this->printList();
	}

	/**
	 * alter(String $type, Array $args)
	 * 修改
	 * --------------------------------
	 * @param String $type 类型
	 * @param Array $args 参数
	 * ------------------------
	 * @author Verdient。
	 */
	protected function alter($type, $args){
		$services = $this->requestServices($args);
		if(!empty($services)){
			$this->info('需操作' . count($services) . '个服务');
			$command = $type . ' ';
			foreach($services as $name){
				$command .= $name . ' ';
			}
			$command = substr($command, 0, -1);
			$this->info('开始执行');
			$this->runPM2Command($command);
			$this->info('操作已完成，请确认各服务运行情况');
			$this->printList();
		}else{
			$this->error('没有需要操作的服务');
		}
	}

	/**
	 * printList()
	 * 打印列表
	 * -----------
	 * @author Verdient。
	 */
	protected function printList(){
		$this->message($this->list());
	}

	/**
	 * list()
	 * 列表
	 * ------
	 * @return Array
	 * @author Verdient。
	 */
	protected function list(){
		return $this->runPM2Command('list');
	}
}