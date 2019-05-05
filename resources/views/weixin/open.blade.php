<h3>微信用户得openid</h3>
<table border='1'>
    <tr>
        <td><input type="checkbox" class="box" openid="{{$openid1}}">{{$openid1}}</td>
        <td><input type="checkbox" class="box" openid="{{$openid2}}">{{$openid2}}</td>
        <td><input type="checkbox" class="box" openid="{{$openid3}}">{{$openid3}}</td>
    </tr>
</table>
<input type="text"  class="text" required  lay-verify="required" placeholder="请输入群发内容" autocomplete="off" class="layui-input">
<button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
<script src="/js/jquery-3.2.1.min.js"></script>
<script>
$('.layui-btn').click(function(){
           var _val = $('.text').val();
           console.log(_val);
    
            var box = $('.box');
            var openid = "";
            box.each(function(index){
                if($(this).prop('checked') == true){
                    openid += $(this).attr('openid')+",";
                }
            })
            openid = openid.substr(0,openid.length-1);
            // console.log(openid);
            location.href="contents?openid="+openid+'&name='+_val;
       })
</script>