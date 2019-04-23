<h3>商品列表</h3>
<table border="1">
    <tr>
        <th>排序</th>
        <th>商品名称</th>
        <th>商品价格</th>
        <th>商品库存</th>
    </tr>
    @foreach($res as $k=>$v)
    <tr>
        <td><a href='goodsdetail?g_id={{$v-> g_id}}'>{{$v-> g_id}}</td>
        <td>{{$v-> goods_name}}</td>
        <td>{{$v-> goods_price}}</td>
        <td>{{$v-> goods_num}}</td>
    </tr>
    @endforeach
</table>