# MySQL dump 8.16
#
# Host: localhost    Database: IntraDayData
#--------------------------------------------------------
# Server version	3.23.47

#
# Table structure for table 'hourlyindicators'
#


DROP TABLE IF EXISTS hourlyindicators;
CREATE TABLE hourlyindicators(
  ISIN CHAR(15) default NULL,
  TimeDate datetime default NULL,
  Period integer default NULL,
  AvgPrice float default NULL,
  AvgVol bigint(20) default NULL,
  StddevPrice float default NULL,
  Ismin tinyint default NULL,
  Ismax tinyint default NULL,
  UNIQUE KEY I_hourlyindicators (ISIN, TimeDate, Period)
) ;

