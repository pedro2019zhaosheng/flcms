<?php

namespace app\vp\controller;

use app\common\VpController;
use app\common\Helper;
use think\Db;

/**
 * 广告管理控制器
 */
class Adver extends VpController
{

    /**
     * 广告与类型 列表
     *
     * @param int $isDel
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index($isDel = 0)
    {
        $get = $this->get;
        //type为真 请求广告类型数据 反之 请求广告列表
        if (!empty($get['type'])) {
            $model = new \app\common\model\CmsAdType();
            $pagination = $model->getList($get);
        } else {
            $model = new \app\common\model\CmsAd();
            $pagination = $model->getList($get, $isDel);
        }
        $page = $pagination->render();
        $list = $pagination->toArray();
        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 添加广告
     * @throws \Exception
     */
    public function add()
    {
        $post = $this->post;
        $validation = $this->validate($post, "adver.add");
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $data = [
            'name' => $post['name'],
            'ad_type' => $post['type'],
            'url' => $post['url'],
            'status' => $post['status'],
            'abstract' => $post['abstract'],
            'sort' => 50,
            'is_del' => 0,
            'create_time' => Helper::timeFormat(time(), 's'),
            'update_time' => Helper::timeFormat(time(), 's'),
        ];
        if (!empty($post['file'])) {
            $return = Helper::uploadImage('base64', 'advise');
            if (!is_array($return)) {
                return $this->asJson(0, 'error', '上传头像失败，错误信息: ' . $return);
            }
            $head = $return['head'];
            $data['img'] = $head;
        }

        $model = new \app\common\model\CmsAd();
        $result = $model->insertAdver($data);
        if ($result) {
            return $this->asJson(1, 'success', '新增成功');
        }

        return $this->asJson(0, 'error', '新增失败');
    }

    /**
     * 添加广告类型
     */
    public function addType()
    {
        $post = $this->post;
        $validation = $this->validate($post, "AdverType.add");
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }
        $data = [
            'name' => $post['name'],
            'status' => $post['status'],
            'create_time' => Helper::timeFormat(time(), 's'),
            'update_time' => Helper::timeFormat(time(), 's'),
        ];
        $model = new \app\common\model\CmsAdType();
        $result = $model->insertAdver($data);
        if ($result) {
            return $this->asJson(1, 'success', '新增成功');
        }

        return $this->asJson(0, 'error', '新增失败');
    }

    /**
     * 获取广告类型
     * @return [type] [description]
     */
    public function getTypeAll()
    {
        $list = Db::name('cms_ad_type')
            ->field('id,name')
            ->where('status', 1)
            ->select();
        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 广告删除管理
     */
    public function delete()
    {
        $get = $this->get;
        if (!isset($get['id'])) {
            return $this->asJson(0, 'error', '删除失败');
        }
        $model = new \app\common\model\CmsAd();
        //is_del为真则删除 为假则改变软删除   status存在既还原操作
        if (!empty($get['is_del'])) {
            $result = $model->destroy((int)$get['id']); //物理删除
        } elseif (!empty($get['status'])) {
            $result = $model->deleteAdver((int)$get['id'], 0); //还原
        } else {
            $result = $model->deleteAdver((int)$get['id']); //软删除
        }
        if ($result) {
            return $this->asJson(1, 'success', '删除成功');
        }

        return $this->asJson(0, 'error', '删除失败');
    }


    /**
     * 广告类型删除管理
     */
    public function deleteType()
    {
        $get = $this->get;
        if (!isset($get['id'])) {
            return $this->asJson(0, 'error', '删除失败');
        }
        $model = new \app\common\model\CmsAdType();
        $result = $model->destroy((int)$get['id']);
        if ($result) {
            return $this->asJson(1, 'success', '删除成功');
        }

        return $this->asJson(0, 'error', '删除失败');
    }

    /**
     * 获取广告 或 类型 详情
     * @param $id // 广告ID 或 类型 id
     * @return \think\response\Json
     */
    public function info($id)
    {
        $get = $this->get;
        if (!empty($get['type'])) {
            $model = \app\common\model\CmsAdType::quickGetOne($id);
            $data = [
                'name' => $model->name,
                'status' => $model->status,
            ];
        } else {
            $model = \app\common\model\CmsAd::quickGetOne($id);
            $data = [
                'adType' => $model->ad_type,
                'status' => $model->status,
                "name" => $model->name,
                'abstract' => $model->abstract,
            ];
        }
        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 修改广告 或 类型 信息
     *  adverType 为真则修改 类型 反之 修改广告
     */
    public function modify()
    {
        $post = $this->post;
        if (!empty($post['adverType'])) {
            \app\common\model\CmsAdType::quickCreate([
                'id' => $post['id'],
                'name' => $post['name'],
                'status' => $post['status'],
            ], true);
        } else {
            \app\common\model\CmsAd::quickCreate([
                'id' => $post['id'],
                'name' => $post['name'],
                'ad_type' => $post['type'],
                'abstract' => $post['abstract'],
                'status' => $post['status'],
            ], true);
        }
        return $this->asJson(1, 'success', '修改成功');
    }

    /**
     * 回收站列表
     * @return [type] [description]
     */
    public function recycle()
    {
        return $this->index($isDel = 1);
    }

    //设置广告 或 类型 状态
    public function setStatus()
    {
        $post = $this->post;
        //type为真则更新广告类型表 反之则更新广告表
        if (!empty($post['type'])) {
            $table = 'cms_ad_type';
        } else {
            $table = 'cms_ad';
        }
        $res = Db::name($table)
            ->where('id', 'in', $post['id'])
            ->update(['status' => $post['status']]);
        return $this->asJson(1, 'success', '修改成功');
    }

}
