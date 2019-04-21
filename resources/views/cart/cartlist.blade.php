<h3>购物车列表</h3>
<table>
    <tr>
        <th>排序</th>
        <th>商品名称</th>
        <th>商品价格</th>
    </tr>
    @foreach($cartInfo as $k=>$v)
    <tr>
        <td>{{$v['c_id']}}</td>
        <td>{{$v['goods_name'] }}</td>
        <td>{{$v['goods_price'] }}</td>
    </tr>
    @endforeach
</table>
<h5>商品总价：{{$zong_price}}</h5> 
<a href="order">去结算</a>