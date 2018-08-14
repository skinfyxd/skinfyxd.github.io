- <b>简体中文</b>
- [English](https://github.com/printempw/blessing-skin-server/blob/master/README_EN.md)

<p align="center"><img src="https://img.blessing.studio/images/2017/01/01/bs-logo.png"></p>

<p align="center">
<a href="https://travis-ci.org/printempw/blessing-skin-server"><img src="https://api.travis-ci.org/printempw/blessing-skin-server.svg?branch=master" alt="Travis Building Status"></a>
<a href="https://codecov.io/gh/printempw/blessing-skin-server"><img src="https://codecov.io/gh/printempw/blessing-skin-server/branch/master/graph/badge.svg" alt="Codecov" /></a>
<a href="https://github.com/printempw/blessing-skin-server/releases"><img src="https://poser.pugx.org/printempw/blessing-skin-server/version" alt="Latest Stable Version"></a>
<img src="https://img.shields.io/badge/PHP-5.5.9+-orange.svg" alt="PHP 5.5.9+">
<img src="https://poser.pugx.org/printempw/blessing-skin-server/license" alt="License">
<a href="https://twitter.com/printempw"><img src="https://img.shields.io/twitter/follow/printempw.svg?style=social&label=Follow" alt="Twitter Follow"></a>
</p>

优雅的开源 Minecraft 皮肤站，现在，回应您的等待。

Blessing Skin 是一款能让您上传、管理和分享您的 Minecraft 皮肤和披风的 Web 应用程序。与修改游戏材质包不同的是，所有人都能在游戏中看到各自的皮肤和披风（当然，前提是玩家们要使用同一个皮肤站）。

Blessing Skin 是一个开源的 PHP 项目，这意味着您可以自由地在您的服务器上部署它。这里有一个 [演示站点](http://skin.prinzeugen.net/)。

特性
-----------
- 完整实现了一个皮肤站该有的功能
- 支持单用户多个角色
- 通过皮肤库来分享您的皮肤和披风！
- 易于使用
    - 可视化的用户、角色、材质管理页面
    - 详细的站点配置页面
    - 多处 UI/UX 优化只为更好的用户体验
- 安全
    - 支持多种安全密码 Hash 算法
    - 注册可要求 Email 验证（插件）
    - 防止恶意请求的积分系统
- 强大的可扩展性
    - 多种多样的插件
    - 支持与 Authme/Discuz 等程序的用户数据对接
    - 支持自定义 Yggdrasil API 外置登录系统

环境要求
-----------
Blessing Skin 对您的服务器有一定的要求。_在大多数情况下，下列所需的 PHP 扩展已经开启。_

- 一台支持 URL 重写的主机，Nginx、Apache 或 IIS
- **PHP >= 5.5.9** [（服务器不支持？）](https://github.com/printempw/blessing-skin-server/wiki/%E7%89%88%E6%9C%AC%E8%AF%B4%E6%98%8E)
- 安装并启用如下 PHP 扩展：
    - OpenSSL
    - PDO
    - Mbstring
    - Tokenizer
    - GD

如果你使用的是 PHP 7.2，请先阅读 [Wiki - 在 PHP 7.2 上运行](https://github.com/printempw/blessing-skin-server/wiki/%E5%9C%A8-PHP-7.2-%E4%B8%8A%E8%BF%90%E8%A1%8C)。

快速使用
-----------
请参阅 [Wiki - 快速安装向导](https://github.com/printempw/blessing-skin-server/wiki/%E5%BF%AB%E9%80%9F%E5%AE%89%E8%A3%85%E5%90%91%E5%AF%BC)。

![screenshot](https://img.blessing.studio/images/2017/07/29/2017-06-16_15.54.16.png)

插件系统
------------

Blessing Skin 提供了强大的插件系统，您可以通过添加多种多样的插件来为您的皮肤站添加功能。

详情请参阅 [Wiki - 插件系统介绍](https://github.com/printempw/blessing-skin-server/wiki/%E6%8F%92%E4%BB%B6%E7%B3%BB%E7%BB%9F%E4%BB%8B%E7%BB%8D)。

自行构建
------------
如果你想为此项目作贡献，或者抢先尝试未发布的新功能，你应该先用 Git 上的代码部署。

**不推荐不熟悉 shell 操作以及不想折腾的用户使用。**

从 Git 上 clone 源码并安装依赖:

```bash
$ git clone https://github.com/printempw/blessing-skin-server.git
$ composer install
$ yarn install
```

运行自动化测试（可跳过）：

```bash
$ yarn test
$ ./vendor/bin/phpunit
```

构建前端代码！

```bash
$ yarn run build
```

接下来请参考「快速安装向导」进行后续安装。

问题报告
------------
请参阅 [Wiki - 报告问题的正确姿势](https://github.com/printempw/blessing-skin-server/wiki/%E6%8A%A5%E5%91%8A%E9%97%AE%E9%A2%98%E7%9A%84%E6%AD%A3%E7%A1%AE%E5%A7%BF%E5%8A%BF)。

版权
------------
Copyright 2016-2018 printempw and contributors.

Blessing Skin 是基于 GNU General Public License version 3 开放源代码的自由软件，你可以遵照 GPLv3 协议来修改或重新发布本程序。

**例外情况**：任何为 Blessing Skin 皮肤站程序开发、调用了 Blessing Skin 插件 API 的插件程序，在未使用 Blessing Skin 程序源代码的情况下，无须采用 GPLv3 协议，也不强制要求开放插件源代码。

程序原作者为 [@printempw](https://blessing.studio/)，转载请注明。
