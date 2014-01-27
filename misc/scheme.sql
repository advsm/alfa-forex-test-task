
create table product (
  `id` int unsigned not null primary key auto_increment,
  `name` varchar(64) not null unique key comment 'Название товара',
  `price` int unsigned not null comment 'Цена товара, в рублях'
) engine=InnoDB charset=UTF8;

create table product_property_group (
  `id` int unsigned not null primary key auto_increment,
  `name` varchar(64) not null unique key comment 'Название группы свойства товара (цвет, вес, etc)'
) engine=InnoDB charset=UTF8;

create table product_property_value (
  `id` int unsigned not null primary key auto_increment,
  `group_id` int unsigned not null comment 'Связь с названием свойства по ID',
  `value` varchar(64) not null comment 'Значение свойства товара',
  foreign key (`group_id`) references `product_property_group` (`id`)
) engine=InnoDB charset=UTF8;

create table product2property (
  `id` int unsigned not null primary key auto_increment,
  `product_id` int unsigned not null comment 'Связь с товаром по ID',
  `property_value_id` int unsigned not null comment 'Связь со значением свойства по ID',
  unique key (`product_id`, `property_value_id`),
  foreign key (`product_id`) references `product` (`id`),
  foreign key (`property_value_id`) references `product_property_value` (`id`)
) engine=InnoDB charset=UTF8;

insert into product_property_group set id=1, name='Операционная система';
insert into product_property_group set id=2, name='Производитель';
insert into product_property_group set id=3, name='Цвет';
insert into product_property_group set id=4, name='Вес';

insert into product_property_value set id=1, group_id=1, value='IOS';
insert into product_property_value set id=2, group_id=1, value='Windows Phone';
insert into product_property_value set id=3, group_id=1, value='Android';

insert into product_property_value set id=4, group_id=2, value='Apple';
insert into product_property_value set id=5, group_id=2, value='Samsung';
insert into product_property_value set id=6, group_id=2, value='HTC';
insert into product_property_value set id=7, group_id=2, value='LG';

insert into product_property_value set id=8, group_id=3, value='Черный';
insert into product_property_value set id=9, group_id=3, value='Белый';

insert into product_property_value set id=10, group_id=4, value='100';
insert into product_property_value set id=11, group_id=4, value='150';
insert into product_property_value set id=12, group_id=4, value='200';
insert into product_property_value set id=13, group_id=4, value='250';

insert into product set id=1, name='Apple IPhone 5 Black Big', price='28000';
insert into product2property set product_id=1, property_value_id=1;
insert into product2property set product_id=1, property_value_id=4;
insert into product2property set product_id=1, property_value_id=8;
insert into product2property set product_id=1, property_value_id=13;

insert into product set id=2, name='Apple IPhone 5 Black Small', price='29000';
insert into product2property set product_id=2, property_value_id=1;
insert into product2property set product_id=2, property_value_id=4;
insert into product2property set product_id=2, property_value_id=8;
insert into product2property set product_id=2, property_value_id=10;

insert into product set id=3, name='Apple IPhone 5 White Small', price='29000';
insert into product2property set product_id=3, property_value_id=1;
insert into product2property set product_id=3, property_value_id=4;
insert into product2property set product_id=3, property_value_id=9;
insert into product2property set product_id=3, property_value_id=10;

insert into product set id=4, name='Samsung Galaxy S3 Black', price='20000';
insert into product2property set product_id=4, property_value_id=3;
insert into product2property set product_id=4, property_value_id=5;
insert into product2property set product_id=4, property_value_id=8;
insert into product2property set product_id=4, property_value_id=13;

insert into product set id=5, name='Samsung Galaxy S3 White', price='20000';
insert into product2property set product_id=5, property_value_id=3;
insert into product2property set product_id=5, property_value_id=5;
insert into product2property set product_id=5, property_value_id=9;
insert into product2property set product_id=5, property_value_id=13;

insert into product set id=6, name='HTC One Black', price='15000';
insert into product2property set product_id=6, property_value_id=3;
insert into product2property set product_id=6, property_value_id=6;
insert into product2property set product_id=6, property_value_id=8;
insert into product2property set product_id=6, property_value_id=12;

insert into product set id=7, name='HTC One White', price='15000';
insert into product2property set product_id=7, property_value_id=3;
insert into product2property set product_id=7, property_value_id=6;
insert into product2property set product_id=7, property_value_id=9;
insert into product2property set product_id=7, property_value_id=12;

insert into product set id=8, name='HTC One White Windows', price='14000';
insert into product2property set product_id=8, property_value_id=2;
insert into product2property set product_id=8, property_value_id=6;
insert into product2property set product_id=8, property_value_id=9;
insert into product2property set product_id=8, property_value_id=12;

insert into product set id=9, name='LG WinPhone Black Light', price='15000';
insert into product2property set product_id=9, property_value_id=2;
insert into product2property set product_id=9, property_value_id=7;
insert into product2property set product_id=9, property_value_id=8;
insert into product2property set product_id=9, property_value_id=10;

insert into product set id=10, name='LG WinPhone Black Big', price='15000';
insert into product2property set product_id=10, property_value_id=2;
insert into product2property set product_id=10, property_value_id=7;
insert into product2property set product_id=10, property_value_id=8;
insert into product2property set product_id=10, property_value_id=13;

insert into product set id=11, name='LG WinPhone White Big', price='15000';
insert into product2property set product_id=11, property_value_id=2;
insert into product2property set product_id=11, property_value_id=7;
insert into product2property set product_id=11, property_value_id=9;
insert into product2property set product_id=11, property_value_id=13;

