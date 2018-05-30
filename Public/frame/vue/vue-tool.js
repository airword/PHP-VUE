/* 搭配 vue 和vue-router使用  */
/* author airword            */
/* version 1.01              */
window._={
    version:"1.01",
    ajax(options){
        /*提取jquery的ajax*/
        function empty(options) {}
        function obj2Url(obj) {
            if (obj && obj instanceof Object) {
                var arr = [];
                for (var i in obj) {
                    if (obj.hasOwnProperty(i)) {
                        if (typeof obj[i] == 'function') obj[i] = obj[i]();
                        if (obj[i] == null) obj[i] = '';
                        arr.push(escape(i) + '=' + escape(obj[i]));
                    }
                }
                return arr.join('&').replace(/%20/g, '+');
            } else {
                return obj;
            }
        };
        var opt = {
            url: '', //请求地址
            sync: true, //true，异步 | false　同步，会锁死浏览器，并且open方法会报浏览器警告
            method: 'GET', //提交方法
            data: null, //提交数据
            username: null, //账号
            password: null, //密码
            dataType: null, //返回数据类型
            success: empty, //成功返回回调
            error: empty, //错误信息回调
            timeout: 0, //请求超时ms
        };
        for (var i in options) if (options.hasOwnProperty(i)) opt[i] = options[i];
        var accepts = {
            script: 'text/javascript, application/javascript, application/x-javascript',
            json: 'application/json',
            xml: 'application/xml, text/xml',
            html: 'text/html',
            text: 'text/plain'
        };
        var abortTimeout = null;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                xhr.onreadystatechange = empty;
                clearTimeout(abortTimeout);
                var result,dataType, error = false;
                if ((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304 || (xhr.status == 0 && protocol == 'file:')) {
                    if (xhr.responseType == 'arraybuffer' || xhr.responseType == 'blob') {
                        result = xhr.response;
                    } else {
                        result = xhr.responseText;
                        dataType = opt.dataType ? opt.dataType : xhr.getResponseHeader('content-type').split(';', 1)[0];
                        for (var i in accepts) {
                            if (accepts.hasOwnProperty(i) && accepts[i].indexOf(dataType) > -1) dataType = i;
                        }
                        try {
                            if (dataType == 'script') {
                                eval(result);
                            } else if (dataType == 'xml') {
                                result = xhr.responseXML
                            } else if (dataType == 'json') {
                                result = result.trim() == '' ? null : JSON.parse(result)
                            }
                        } catch (e) {
                            opt.error(e, xhr);
                            xhr.abort();
                        }
                    }
                    opt.success(result, xhr);
                } else {
                    opt.error(xhr.statusText, xhr);
                }
            }
        };
        
        xhr.open(opt.method, opt.url, opt.sync, opt.username, opt.password);
        if (opt.method == 'POST') xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        if (opt.timeout > 0) {
            abortTimeout = setTimeout(function() {
                xhr.onreadystatechange = empty
                xhr.abort();
                opt.error('timeout', xhr);
            }, opt.timeout)
        }
        xhr.send(opt.data ? obj2Url(opt.data) : null);
    },
    post(url,data,fn,dataType){
        //基于组件ajax
        this.ajax({
            type:"POST",
            url:url,
            data:data,
            success:fn,
            dataType:dataType
        })
    },
    get(url,fn,dataType){
        //基于组件ajax
        this.ajax({
            type:"GET",
            url:url,
            success:fn,
            dataType:dataType
        })
    },
    router(routes){
        var me=this;
        var router=new VueRouter({routes:routes});
        router.beforeEach((to, from, next) => {
            //路由注入——判断组件内容为空时用同步ajax获取并赋值
            for(var k in to.matched){
                if(!to.matched[k].components.default){
                    me.ajax({
                        url : to.matched[k].meta,
                        sync : false,
                        success : function(str){
                            //将获取的vue组件字符串转成vue组件格式的object
                            var VUE=document.createDocumentFragment();
                            VUE.append(document.createElement("VUE"));
                            VUE.querySelector("VUE").innerHTML=str;
                            var js=VUE.querySelector("script").innerText;
                            to.matched[k].components.default=eval(eval("("+js.slice(js.indexOf("{"),js.lastIndexOf("}")+1)+")"));//数据
                            to.matched[k].components.default.template=VUE.querySelector("vhtml").innerHTML.replace(/\n/g,"");//获取示图
                        }
                    });
                }
            }
            
            next();
          })
        return router;
    }
}