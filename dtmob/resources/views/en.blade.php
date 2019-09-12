<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>点推联盟</title>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes" />
    <!-- 引入样式 -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
</head>
<body>
<style>
.el-select{ width: 100%; }
.el-button--small, .el-button--small.is-round{width: 100%; margin: 6px 0px !important;}
.problem{font-size: 16px; color: #333; text-align: center; margin-top: 60px;}
.answer{font-size: 14px; color: #999; text-align: center; margin-top: 30px;}
</style>

<div id="app">
    <el-form ref="form" :model="paramet" label-width="90px"  size="small" label-position="top">
        <el-form-item label="">
            <el-select v-model="paramet.category" placeholder="请选择活动区域">
                <el-option label="分类1" value="0"></el-option>
                <el-option label="分类2" value="1"></el-option>
            </el-select>
        </el-form-item>

        <el-form-item label="">
            <el-select v-model="paramet.type" placeholder="请选择活动区域">
                <el-option label="顺序练习" value="1"></el-option>
                <el-option label="随机练习" value="2"></el-option>
            </el-select>
        </el-form-item>

        <el-form-item label="">
            <el-select v-model="paramet.showtype" placeholder="请选择活动区域">
                <el-option label="中文" value="1"></el-option>
                <el-option label="英文" value="2"></el-option>
            </el-select>
        </el-form-item>

        <el-form-item>
            <el-button type="primary" @click="setParamet" v-html="'设置'"></el-button>
            <el-button @click="start" v-html="'开始'"></el-button>
        </el-form-item>
    </el-form>

    <p class="problem" @click="show=!show" v-if="problem" v-html="problem"></p>
    <p class="answer" v-if="show && answer" v-html="answer"></p>

</div>
</body>

<!-- 先引入 Vue -->
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<!-- 引入组件库 -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script>
new Vue({
    el: '#app',
    data() {
      return {
        paramet: {
            category: '0',
            type: '1',
            showtype: '1',
        },
        data: [
            {
                category: '分类1',
                content: [
                    {en: "How's it going",                      cn: '怎么样了'},
                    {en: "How are you, Lisa",                   cn: '丽莎，你好吗？'},
                    {en: "I’m pretty good. Thinks for asking",  cn: "我很好谢谢你关心。"},
                    {en: "what’s up with you?",                 cn: "你怎么了？"},
                    {en: "Hi. I’m heading to class",            cn: "嗨，我正要去上班"},
                    {en: "Hey Ron. It’s nice to see you",       cn: "嗨，罗恩。见到你很高兴"},
                    {en: "I’m pretty good. And you?",           cn: "我很好，你呢？"},
                    {en: "I’m doing swell. you",                cn: "我很好，你呢？"},
                    {en: "It’s a great day out",                cn: "这是一个适合外出的好天气"},
                    {en: "do you want to hang out tomorrow",    cn: "你想明天出去玩吗？"},

                    {en: "What is yellow?",                         cn: "什么东西是黄色的。"},
                    {en: "The sky is blue. I like the color blue",  cn: "天空是蓝色的。我喜欢蓝色"},
                    {en: "the grass is green",                      cn: "草是绿色的"},
                    {en: "paper is white",                          cn: "纸是白色的"},
                    {en: "a tomato is red",                         cn: "番茄是红色的。"},
                    {en: "I like grapes. grapes taste good",        cn: "我喜欢葡萄，葡萄很好吃。"},
                    {en: "night time is dark. what color is it",    cn: "夜晚很暗啊。那是什么颜色？"},
                    {en: "Blue an red make purple",                 cn: "蓝色和红色能调成紫色"},
                    {en: "elephants are grey",                      cn: "大象是灰色的"},
                    {en: "a panda is black and white",              cn: "熊猫是黑白色的"},
                ],
            },{
                category: '分类1',
                content: [
                    {en: 'Hello2',                       cn: '你好2'},
                    {en: 'well2',                        cn: '很好2'},
                    {en: 'a little bad2',                cn: '有点糟糕2'}
                ],
            },
        ],


        ongoingData: {},
        problem: '',    //问题
        answer: '',     //答案
        show: false,
        count: 0,
      }
    },
    methods: {
        shuffle: function(arr){
            var length = arr.length, randomIndex, temp;
            while (length) {
                randomIndex = Math.floor(Math.random() * (length--));
                temp = arr[randomIndex];
                arr[randomIndex] = arr[length];
                arr[length] = temp
            }
            return arr;
        },
        setParamet: function() {

            var category = this.paramet.category;
            var type = this.paramet.type;

            if(type=='1')
            {
                this.ongoingData = this.data[category].content;
            }

            if(type=='2')
            {
                this.ongoingData = this.shuffle(this.data[category].content);
            }

            this.count = 0;

            alert("设置成功");
        },

        start: function() {
            var showtype = this.paramet.showtype;

            if(!this.ongoingData[this.count])
            {
                this.count = 0;
                alert('没有了，重新来一次吧');
            }

            if(showtype=='1')
            {
                this.problem = this.ongoingData[this.count].cn;
                this.answer = this.ongoingData[this.count].en;
            }

            if(showtype=='2')
            {
                this.problem = this.ongoingData[this.count].en;
                this.answer = this.ongoingData[this.count].cn;
            }

            this.count += 1;

        },
    }
})
</script>

</html>