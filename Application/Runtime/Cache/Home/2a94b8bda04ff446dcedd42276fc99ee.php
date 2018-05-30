<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>小猪青年聚后台管理系统</title>
    <script src="/Public/js/jquery-1.8.3.min.js"></script>
    <script src="/Public/frame/vue/vue.min.js"></script>
    <script src="/Public/frame/vue/vue-router.js"></script>
    <script src="/Public/frame/vue/vue-tool.js"></script>
    <link rel="stylesheet" href="/Public/frame/iview/iview.css">
    <script src="/Public/frame/iview/iview.js"></script>
    <style scoped>
      html,body,#box{
        width:100%;
        height:100%;
      }
      .layout{
        border: 1px solid #d7dde4;
        background: #f5f7f9;
        position: relative;
        border-radius: 4px;
        overflow: hidden;
        height:100%;
      }
      .layout-header-bar{
        background: #fff;
        box-shadow: 0 1px 1px rgba(0,0,0,.1);
      }
      .layout-logo-left{
        width: 90%;
        height: 30px;
        background: #5b6270;
        border-radius: 3px;
        margin: 15px auto;
      }
      .menu-icon{
        transition: all .3s;
      }
      .rotate-icon{
        transform: rotate(-90deg);
      }
      .menu-item span{
        display: inline-block;
        overflow: hidden;
        width: 69px;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: bottom;
        transition: width .2s ease .2s;
      }
      .menu-item i{
        transform: translateX(0px);
        transition: font-size .2s ease, transform .2s ease;
        vertical-align: middle;
        font-size: 16px;
      }
      .collapsed-menu span{
        width: 0px;
        transition: width .2s ease;
      }
      .collapsed-menu i{
        transform: translateX(5px);
        transition: font-size .2s ease .2s, transform .2s ease .2s;
        vertical-align: middle;
        font-size: 22px;
      }

      .logo{
        width:100%;
        height:70px;
        box-sizing: border-box;
        padding:20px;
        position: relative;
      }
      .logo img{
        width:100%;
        height:100%;
      }
      /*头部*/
      .header-msg{
        float:right;
        padding:10px;
        box-sizing: border-box;
        height:100%;
        line-height:40px;
      }
      .header-msg .avatar{
        width:30px;
        height:30px;
        background:#F8F8F9;
        border:1px solid #E9EAEC;
        display:inline-block;
        vertical-align:middle;
        border-radius: 15px;
        margin:0 10px;
      }
    </style>
  </head>
  <body>
    <div id="box">
      <div class="layout">
        <Layout style="height:100%;">
          <Sider ref="side1" hide-trigger collapsible :collapsed-width="78">
            <div class="logo"><img :src="logo" alt="天问信息"></div>
            <!-- 菜单（SETTING获取） -->
            <i-menu :open-names="menuStatus.open" :active-name="menuStatus.active" theme="dark" width="auto" class="menu-item" id="MENU"></i-menu>
          </Sider>
          <Layout>
            <!--  头部内容  -->
            <i-header :style="{padding: 0}" class="layout-header-bar">
              <div class="header-msg">
                <Dropdown @on-click="ClickDownMenu">
                    <a href="javascript:void(0)">
                        {{account.user}}
                      <Icon type="arrow-down-b"></Icon>
                    </a>
                    <dropdown-menu slot="list" id="DOWNMENU"></dropdown-menu>
                </Dropdown>
                <img class="avatar" :src="account.avatar">
              </div>
            </i-header>
            <!--  主体内容  -->
            <i-content :style="{background: '#eee', minHeight: '260px',position:'relative',padding:'20px'}">
              <!-- 路由渲染内容 -->
              <router-view></router-view>
            </i-content>
          </Layout>
        </Layout>
      </div>
    </div>
    
    <script>
     a= new Vue({
      el:"#box",
      data:{
          logo:"http://n.skywen.cn/main/img/skywen.svg",
          account:{
            user:"",
            avatar:""
          },
          menuStatus:{
            open:"0",//打开的列表
            active:"0",//打开的地址
          },
          menu:[//菜单
            {name:"欢迎",icon:"ios-navigate",url:"/"},
            {name:"活动管理",icon:"ios-gear",children:[
              {name:"瑞丰ETC充值活动",icon:"ios-navigate",url:"/etc"}
            ]},
            {name:"空间管理",icon:"ios-navigate",url:"/ggk"}
          ],
          downMenu:[//下拉菜单
            {name:"person",label:"个人中心",disabled:false},
            {name:"exit",label:"退出",divided:true},
          ]
        },
        created(){
          this.LoadAccount();
          this.LoadMenu();
          this.LoadDownMenu();
          this.LoadMenuStatus();
        },
        methods:{
          LoadAccount(){
            //获取用户信息
            var me=this;
            if(localStorage.xzqnj){
              //获取缓存用户数据和用户时间
              var account=JSON.parse(localStorage.xzqnj);
              me.account=account;
            }
            else{
              
            }
          },
          LoadMenu(){
            //装载菜单（vue渲染前）
            var s="";
            this.menu.forEach(function(e,i){
              if(!e.children){
                s+='<router-link to="'+e.url+'"><menu-item name="'+i+'"><icon type="'+e.icon+'"></icon><span>'+e.name+'</span></menu-item></router-link>';
              }
              else{
                s+='<Submenu name="'+i+'">';
                s+='<template slot="title"><icon type="'+e.icon+'"></icon><span>'+e.name+'</span></template>';
                e.children.forEach(function(E,I){
                s+='<router-link to="'+E.url+'"><menu-item name="'+i+"-"+I+'"><icon type="'+E.icon+'"></icon><span>'+E.name+'</span></menu-item></router-link>';
                })
                s+='</Submenu>';
              }
            });
            document.getElementById("MENU").innerHTML=s;
          },
          LoadDownMenu(){
            //装载下拉菜单（vue渲染前）
            var s="";
            this.downMenu.forEach(function(e,i){
              s+='<dropdown-item name="'+e.name+'" '+(e.disabled?'disabled':'')+' '+(e.divided?'divided':'')+' >'+e.label+'</dropdown-item>';
            });
            document.getElementById("DOWNMENU").innerHTML=s;
          },
          ClickDownMenu(menuName){
            //下拉菜单按钮事件
            switch(menuName){
              case "person"://个人中心
                alert(2);
                break;
              case "exit"://退出登陆
                location.href="/Admin/Login/logout";
                break;
            }
          },
          LoadMenuStatus(){
            //保持菜单状态
            var me=this;
            var hash="/"+location.hash.slice(1).split("/")[1];
            var menu=this.menu;
            for (var i=0;i<menu.length;i++){
              if(menu[i].url&&menu[i].url==hash){
                me.menuStatus.open=i.toString();
                me.menuStatus.active=i.toString();
              }
              else if(menu[i].children){
                menu[i].children.forEach(function(E,I){
                  if(E.url==hash){
                    me.menuStatus.open=i.toString();
                    me.menuStatus.active=i+"-"+I;
                  }
                })
              }
            }
          }
        },
        router:_.router([
          { path: '/',meta:"/Public/module/home/vhtml/admin.html"},
          { path: '/wkx',meta:"vhtml/wkxTab.html",
            children: [
              { path: '',meta: "vhtml/wkx.html"},
              { path: '1',meta:"vhtml/wkx1.html" }
            ]
          }
        ])
      });
     
    </script>
  </body>
</html>