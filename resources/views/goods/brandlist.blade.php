<table>
    <tr>
        <td>商品名字</td>
        <td>商品图片</td>
    </tr>
    @foreach($res as $k=>$v)
    <tr>
        <td>{{$v-> goods_name}}</td>
        <td><img src="{{'/uploads/goodsimg/'.$v->goods_img }}" alt="" width="50" height="50"></td>
    </tr>
    @endforeach
</table>