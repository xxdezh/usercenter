<div class="div-main">

    <div class="f-head clearfix pos-r bgfff">
        <div class="f-head-logo fl"><a href="<?php echo $this->_siteUrl;?>/house/site/index/city/<?php echo Cookie::get('city') ?>"><img src="<?php echo $this->_siteUrl;?>/assets/house/images/f-head-logo.png" alt="腾讯楼盘商城" /></a></div>
        <div class="f-head-selectcity pos-r fl">
            <span class="fs28">

                <?php
                switch (Cookie::get('city'))
                {
                    case 1:
                        echo "武汉";
                    break;
                    case 2:
                        echo "郑州";
                    break;
                    case 3:
                        echo "重庆";
                        break;
                    default:
                        echo "武汉";
                }
                ?>
            </span>
        </div>
        <div class="f-head-help fr">
            <ul>
               <!--  <li><a href=""><i class="icon-help"></i></a></li> -->
                <li><a class="icon-member-a fs48 fc777" href="<?php echo $this->createUrl('/house/member/index',array('id'=>$this->member['id'])) ?>"><i class="icon-member"></i>
                    <span>个人中心</span>     
                </a></li>
            </ul>
        </div>

        <div class="f-head-selectcitydiv">
		   			<span>
		   				<a href="<?php echo $this->createUrl('/house/site/index', array('city' => 1)) ?>">武汉</a>
		   				<a href="<?php echo $this->createUrl('/house/site/index', array('city' => 2)) ?>">郑州</a>
		   				<a href="<?php echo $this->createUrl('/house/site/index', array('city' => 3)) ?>">重庆</a>
		   			</span>
        </div>
    </div>
    <div class="pos-r bb"></div>