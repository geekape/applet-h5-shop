<template>
  <div class="goods-list">
    <a :href="'../goods_detail/main?id='+item.id"  hover-class="none" class="goods-list__item" v-for="(item, index) in goodsData" :key="item.id">
        <img class="goods-list__img" :src="item.cover" lazy-load/>
        <div class="goods-list__ft">
            <p class="goods-list__tt overflow-dot">{{item.title}}</p>
            <span class="goods-list__price s-red"><span class="icon-price">¥</span>{{item.sale_price == 0 ? item.market_price : item.sale_price}}</span>
            <span class="goods__price-cost" v-if="item.market_price > item.sale_price && item.sale_price != 0">¥{{item.market_price}}</span>
        </div>
    </a>
  </div>
</template>

<script>
export default {
  props: ['goodsData']
}
</script>

<style lang="scss" scoped>

.goods-list {
    display: flex;
    flex-wrap: wrap;
    padding: 10px 15px;
    &__item {
        flex: 48%;
        max-width: 50%;
        box-sizing:border-box;
        margin-bottom: $box-size;
        &:nth-child(even) {margin-left: 5px;}
        &:nth-child(odd) {margin-right: 5px;}
    }
    &__img {
        width: 100%;
        height: 180px;
        border-radius:10px 10px 0 0;
        background: #eaeaea;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    &__ft {
        margin-top: -7px;
        background: #f9f9f9;
        padding: 10px;
    }
    &__tt {
        padding-bottom: 10px;
        height:35px;
        font-size:16px;
        line-height: 1.4;
    }
}

.goods__price-cost {
    margin-left: 5px;
}
    
</style>
