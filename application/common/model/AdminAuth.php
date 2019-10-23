<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/8
 * Time: 17:29
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;

/**
 * 权限模型
 *
 * Class AdminAuth
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AdminAuth extends BaseModel
{
    /**
     * 访问授权获取权限树
     *
     * @param int $roleId // 角色ID
     * @param int $id // 节点ID
     * @param array $container // 数据容器
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getAuthTree($roleId, $id = 0, array &$container = [])
    {
        $model = AdminMenu::where('pid', $id)
            ->field(['id', 'title', 'icon', 'menu_type', 'status'])
            ->select();

        $layer = $model->toArray();
        foreach ($layer as $key => $item) {
            $data = [
                'id' => $item['id'],
                'text' => $item['title'],
                'children' => []
            ];

            if ($item['status'] === 0) {
                $data['state']['disabled'] = 'true';
            }

            $menuId = $item['id'];
            $authId = self::where([
                ['role_id', '=', $roleId],
                ['menu_id', '=', $menuId],
                ['js_type', '=', 'clicked']
            ])->value('id');
            if ($authId) {
                $data['state']['selected'] = 'true';
            }

            if (!empty($item['icon'])) {
                $data['icon'] = $item['icon'];
            } else {
                $data['type'] = 'menu';
            }

            if (strcasecmp($item['menu_type'], 'function')) {
                $data['state']["opened"] = 'true';
            }

            $container[$key] = $data;
            $this->getAuthTree($roleId, $item['id'], $container[$key]['children']);
        }

        return $container;
    }

    /**
     * 获取角色允许访问的菜单
     *
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMenu()
    {
        if (!\cache('?' . UID)) {
            $innerMenu = $this->getRoleMenu();
            $menu = $this->setMenu($innerMenu);
            // 缓存时间一天
            \cache(UID, $menu, 86400);
        } else {
            $menu = \cache(UID);
        }

        return $menu;
    }

    /**
     * 菜单递归拼接
     *
     * @param int $id // 菜单ID
     * @param string $container // html容器
     * @param null|boolean $isAdmin // 是否是超级管理员
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getRoleMenu($id = 0, &$container = '', $isAdmin = null)
    {
        // 获取角色类型
        if (is_null($isAdmin)) {
            $isAdmin = self::isAdmin();
        }
        // 判断是否是超级管理员
        if ($isAdmin) {
            $model = AdminMenu::where('menu_type', 'in', ['link', 'module', 'single'])
                ->where('pid', $id)
                ->field([
                    'id',
                    'title',
                    'icon',
                    'menu_type',
                    'url_value',
                ])
                ->order('sort')
                ->select();
        } else {
            $model = self::where('a.role_id', ROLE)
                ->where('m.menu_type', 'in', ['link', 'module', 'single'])
                ->where('pid', $id)
                ->alias('a')
                ->field([
                    'm.id',
                    'title',
                    'icon',
                    'menu_type',
                    'url_value',
                ])
                ->leftJoin('admin_menu m', 'a.menu_id = m.id')
                ->order('sort')
                ->select();
        }

        $layer = $model->toArray();
        foreach ($layer as $key => $item) {
            switch ($item['menu_type']) {
                case 'single':
                    $container .= '<li class="submenu">' . PHP_EOL;
                    $container .= '<a id="module_' . $item['id'] . '" target="_self" href="' . $item['url_value'] . '">' . PHP_EOL;
                    $container .= '<i class="' . $item['icon'] . '"></i><span>' . $item['title'] . '</span>' . PHP_EOL;
                    $container .= '</a>' . PHP_EOL;
                    $container .= '</li>' . PHP_EOL;
                    break;
                case 'module':
                    $inner = '';
                    $this->getRoleMenu($item['id'], $inner, $isAdmin);
                    $container .= '<li class="submenu">' . PHP_EOL;
                    $container .= '<a id="module_' . $item['id'] . '" href="#"><i class="' . $item['icon'] . '">' . PHP_EOL;
                    $container .= '</i><span>' . $item['title'] . '</span><span class="menu-arrow"></span></a>' . PHP_EOL;
                    $container .= '<ul class="list-unstyled">' . PHP_EOL;
                    $container .= $inner . PHP_EOL;
                    $container .= '</ul>' . PHP_EOL;
                    $container .= '</li>' . PHP_EOL;
                    break;
                case 'link':
                    $container .= '<li><a target="_self" href="' . $item['url_value'] . '">' . $item['title'] . ' </a></li>' . PHP_EOL;
                    break;
            }
        }

        return $container;
    }

    /**
     * 拼接菜单外层
     *
     * @param string $innerMenu
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function setMenu($innerMenu = '')
    {
        $leftNav = '<div class="left main-sidebar animated slideInLeft">' . PHP_EOL;
        $leftNav .= '<div class="sidebar-inner leftscroll">' . PHP_EOL;
        $leftNav .= '<div id="sidebar-menu">' . PHP_EOL;
        $leftNav .= '<ul>' . PHP_EOL;
        $leftNav .= $innerMenu . PHP_EOL;
        $leftNav .= '</ul>' . PHP_EOL;
        $leftNav .= '<div class="clearfix"></div>' . PHP_EOL;
        $leftNav .= '</div>' . PHP_EOL;
        $leftNav .= '<div class="clearfix"></div>' . PHP_EOL;
        $leftNav .= '</div>' . PHP_EOL;
        $leftNav .= ' </div>' . PHP_EOL;

        return $leftNav;
    }

    /**
     * 保存权限
     *
     * @param $roleId // 角色ID
     * @param $menuId // 节点ID
     * @param $type // js选中类型
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function insertAuth($roleId, $menuId, $type)
    {
        $result = self::quickCreate([
            'role_id' => $roleId,
            'menu_id' => $menuId,
            'js_type' => $type
        ]);

        return $result == true;
    }

    /**
     * 判断当前用户是否有访问当前URL的权限
     *
     * @param $url // 当前url(不带域名，格式：如，/vp/admin)
     * @return bool // true 有权限   false 没权限
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function hasAuth($url)
    {
        $isAdmin = self::isAdmin();
        // 如果是超级管理员，直接返回true
        if ($isAdmin) {
            return true;
        }

        // 不是超级管理员
        $authId = self::where('a.role_id', ROLE)
            ->where('url_value', $url)
            ->alias('a')
            ->leftJoin('admin_menu m', 'a.menu_id = m.id')
            ->value('a.id');

        if (!empty($authId)) {
            return true;
        }

        $menuId = AdminMenu::where('url_value', $url)->value('id');

        if (empty($menuId)) {
            return true;
        }

        return false;
    }

    /**
     * 判断是否是超级管理员
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function isAdmin()
    {
        $roleData = AdminRole::get(ROLE);
        // 没绑定角色直接返回false
        if (empty($roleData)) {
            return false;
        }

        // 超级管理员
        if ((int)$roleData['roletype'] === 0) {
            return true;
        }

        return false;
    }
}