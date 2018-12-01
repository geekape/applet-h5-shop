<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

namespace Home\Logic;

/**
 * 文档模型子模型 - 下载模型
 */
class DownloadLogic extends BaseLogic{

	public function update($id){
		/* 获取下载数据 */ //TODO: 根据不同用户获取允许更改或添加的字段
		$data = input('post.');
		if(!$data){
			return false;
		}

		$file = json_decode(think_decrypt(I('post.file')), true);
		if(!empty($file)){
			$data['file_id'] = $file['id'];
			$data['size']    = $file['size'];
		} else {
			$this->error = '获取上传文件信息失败！';
			return false;
		}
		
		/* 添加或更新数据 */
		if(empty($data['id'])){//新增数据
			$data['id'] = $id;
			$id = $this->insertGetId($data);
			if(!$id){
				$this->error = '新增详细内容失败！';
				return false;
			}
		} else { //更新数据
			$status = $this->isUpdate(true)->save($data);
			if(false === $status){
				$this->error = '更新详细内容失败！';
				return false;
			}
		}

		return true;
	}

	/**
	 * 下载文件
	 * @param  number $id 文档ID
	 * @return boolean    下载失败返回false
	 */
	public function download($id){
		$info = $this->where('id', $id)->find();
		if(empty($info)){
			$this->error = "不存在的文档ID：{$id}";
			return false;
		}

		$File = D('home/File');
		$root = config('DOWNLOAD_UPLOAD.rootPath');
		$call = array($this, 'setDownload');
		if(false === $File->download($root, $info['file_id'], $call, $info['id'])){
			$this->error = $File->getError();
		}
	}

	/**
	 * 新增下载次数（File模型回调方法）
	 */
	public function setDownload($id){
		$map = array('id' => $id);
		$this->where( wp_where($map) )->setInc('download');
	}

}
