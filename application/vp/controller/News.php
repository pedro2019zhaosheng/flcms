<?php

namespace app\vp\controller;

use app\common\VpController;
use app\common\Helper;
use think\Db;

/**
 * 新闻管理控制器
 */
class News extends VpController
{
    /**
     * 新闻与类型 列表
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
        //type为真 请求新闻类型数据 反之 请求新闻列表
        if (!empty($get['type'])) {
            $model = new \app\common\model\CmsNewsType();
            $pagination = $model->getList($get);
        } else {
            $model = new \app\common\model\CmsNews();
            $pagination = $model->getList($get, $isDel);
        }
        $page = $pagination->render();
        $list = $pagination->toArray();
        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 添加新闻
     * @throws \Exception
     */
    public function add()
    {
        $post = $this->post;
        $validation = $this->validate($post, "news.add");
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }
        $data = [
            'title' => $post['title'],
            'news_type' => $post['type'],
            'abstract' => $post['abstract'],
            'status' => $post['status'],
            'content' => $post['content'],
            'sort' => 50,
            'is_del' => 0,
            'create_time' => Helper::timeFormat(time(), 's'),
            'update_time' => Helper::timeFormat(time(), 's'),
        ];
        if (!empty($post['file'])) {
            $return = Helper::uploadImage('base64', 'news');
            if (!is_array($return)) {
                return $this->asJson(0, 'error', '上传图失败，错误信息: ' . $return);
            }
            $head = $return['head'];
            $data['img'] = $head;
        }

        $model = new \app\common\model\CmsNews();
        $result = $model->insertNews($data);
        if ($result) {
            return $this->asJson(1, 'success', '新增成功');
        }

        return $this->asJson(0, 'error', '新增失败');
    }

    /**
     * 添加新闻类型
     */
    public function addType()
    {
        $post = $this->post;
        $validation = $this->validate($post, "NewsType.add");
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }
        $data = [
            'name' => $post['name'],
            'status' => $post['status'],
            'create_time' => Helper::timeFormat(time(), 's'),
            'update_time' => Helper::timeFormat(time(), 's'),
        ];
        $model = new \app\common\model\CmsNewsType();
        $result = $model->insertAdver($data);
        if ($result) {
            return $this->asJson(1, 'success', '新增成功');
        }

        return $this->asJson(0, 'error', '新增失败');
    }

    /**
     * 获取新闻类型
     * @return [type] [description]
     */
    public function getTypeAll()
    {
        $list = Db::name('cms_news_type')
            ->field('id,name')
            ->where('status', 1)
            ->select();
        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 新闻删除管理
     */
    public function delete()
    {
        $get = $this->get;
        if (!isset($get['id'])) {
            return $this->asJson(0, 'error', '删除失败');
        }
        $model = new \app\common\model\CmsNews();
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
     * 新闻类型删除管理
     */
    public function deleteType()
    {
        $get = $this->get;
        if (!isset($get['id'])) {
            return $this->asJson(0, 'error', '删除失败');
        }
        $model = new \app\common\model\CmsNewsType();
        $result = $model->destroy((int)$get['id']);
        if ($result) {
            return $this->asJson(1, 'success', '删除成功');
        }

        return $this->asJson(0, 'error', '删除失败');
    }

    /**
     * 获取新闻 或 类型 详情
     * @param $id // 新闻ID 或 类型 id
     * @return \think\response\Json
     */
    public function info($id)
    {
        $get = $this->get;
        if (isset($get['type']) && !empty($get['type'])) {
            $model = \app\common\model\CmsNewsType::quickGetOne($id);
            $data = [
                'name' => $model->name,
                'status' => $model->status,
            ];
        } else {
            $model = \app\common\model\CmsNews::quickGetOne($id);
            $data = [
                'newsType' => $model->news_type,
                'status' => $model->status,
                "name" => $model->title,
                'abstract' => $model->abstract,
            ];
        }
        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     *  修改新闻 或 类型 信息
     *  newsType 为真则修改 类型 反之 修改新闻
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function modify()
    {
        $post = $this->post;
        if (isset($post['newsType']) && !empty($post['newsType'])) {
            \app\common\model\CmsNewsType::quickCreate([
                'id' => $post['id'],
                'name' => $post['name'],
                'status' => $post['status'],
            ], true);
        } else {
            $data = [
                'id' => $post['id'],
                'title' => $post['name'],
                'news_type' => $post['type'],
                'abstract' => $post['abstract'],
                'status' => $post['status'],
            ];

            if (!empty($post['file'])) {
                $return = Helper::uploadImage('base64', 'news');
                if (!is_array($return)) {
                    return $this->asJson(0, 'error', '上传图失败，错误信息: ' . $return);
                }

                $head = $return['head'];
                $data['img'] = $head;
            }

            \app\common\model\CmsNews::quickCreate($data, true);
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
        //type为真则更新新闻类型表 反之则更新新闻表
        if (!empty($post['type'])) {
            $table = 'cms_news_type';
        } else {
            $table = 'cms_news';
        }
        Db::name($table)
            ->where('id', 'in', $post['id'])
            ->update(['status' => $post['status']]);

        return $this->asJson(1, 'success', '修改成功');
    }
}
