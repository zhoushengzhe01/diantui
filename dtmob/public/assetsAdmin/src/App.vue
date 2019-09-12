<template>
<div id="app">
    <el-container>
        <el-container>
            <el-aside width="168px">
	            <el-row class="tac">

	            	<div class="logo">
						<img src="/website/images/logo.png"/>
					</div>

					<div class="money_box">
						08月21日 星期三
						<br/>
						<router-link to="/admin/user">欢迎 "{{group.user.nickname}}"</router-link>
					</div>
					<br/>

				    <el-menu
				      	background-color="#545c64"
				      	text-color="#fff"
				      	:default-active="group.page"
						:router="true">
						<template v-for="menu in group.menus">
							<!--一级导航-->
							<el-menu-item :index="menu.url" v-if="!menu.list.length">
								<i :class="menu.icon"></i>
								<span slot="title">{{ menu.name }}</span>
							</el-menu-item>

							<!--二级导航-->
							<el-submenu :index="menu.url" v-if="menu.list.length">
								<template slot="title">
									<i :class="menu.icon"></i>
									<span>{{ menu.name }}</span>
								</template>
								<el-menu-item v-for="item in menu.list" :key="item" :index="item.url">{{item.name}}</el-menu-item>
							</el-submenu>
							
						</template>
						
						<el-menu-item index="/admin/user">
				        	<i class="el-icon-edit-outline"></i>
				        	<span slot="title">推出登陆</span>
				      	</el-menu-item>

				    </el-menu>

				</el-row>

			</el-aside>

            <el-container>
            	<el-main>
					<router-view @message="message"></router-view>
				</el-main>
        	</el-container>
    	</el-container>
    </el-container>
</div>
</template>

<script>
export default {
	name: 'app',
	data: function () {	
		return {
			isActive: true,
			group: Group,
			advertiser: Group.advertiser,		
		};
	},
	created: function () {

	},
	methods:{
		Logout: function()
		{
			var Th = this;

            Th.loading = true;

            Th.$api.put('webmaster/logout.json', {}, function(data){
                
                Th.message('success', '推出成功');

                window.location.href = '/login/web';

            }, function(type, message){ Th.message(type, message) });
		},

		message: function(type, message){
            this.$message({ showClose: true, message: message, type: type });
            this.loading = false;
        }
	},
}
</script>