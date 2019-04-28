<h3>商品详情</h3>
<table>
    <tr>
        <td>商品名字</td>
        <td>商品价格</td>
    </tr>
 
    <tr>
        <td>{{$result-> goods_id}}</td>
        <td>{{$result-> goods_name}}</td>
    </tr>
   
</table>
<hr>
<br>
<h3>根据浏览次数排序</h3>
<table border=1>
    <tr>
        <td>商品id</td>
        <td>商品名字</td>
        <td>商品浏览次数</td>
    </tr>
    @foreach($res as $k=>$v)
    <tr>
       
            <td>{{$v['goods_id']}}</td>
            <td>{{$v['goods_name']}}</td>
            <td>{{$v['goods_look']}}</td>
    </tr>
    @endforeach
</table>
<hr>
<br>
<h3>浏览历史</h3>
<table>
        <tr>
            <td>商品id</td>
            <td>商品名字</td>
            <td>商品浏览时间</td>
        </tr>
        @foreach($detailinfo as $key=>$val)
        <tr>
            <td>{{$val->goods_id}}</td>
            <td>{{$val->goods_name}}</td>
            <td>{{$val-> look_time}}</td>
        </tr>
        @endforeach
</table>
<div id='qrcode'></div>
<script src="/js/jquery-3.2.1.min.js"></script>
        <script src="/js/qrcodes.js"></script>
<script type="text/javascript">
            new QRCode(document.getElementById("qrcode"), "{{$server}}");
</script>