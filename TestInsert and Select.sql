/*ALTER IGNORE TABLE WalkerData ADD CONSTRAINT WalkerData_unique
UNIQUE (Location, InOrOut, WeekDay, DateTime); */

#insert into WalkerData (Location, InOrOut, WeekDay, DateTime) values ("Student Recreation Center", TRUE, dayofweek(@Now), "2018-02-15 19:49:43");

#select * from walkerData where dayofweek(DateTime) = 5

#ALTER TABLE WalkerData AUTO_INCREMENT=1;

#select * from WalkerData;

#SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN "2018-02-06%" AND "2018-02-14%" 

#SELECT * FROM WalkerData WHERE DateTime BETWEEN "2018-02-02%" AND "2018-02-03%";

#show create table WalkerData

#INSERT INTO WalkerData (Location, InOrOut, WeekDay, DateTime) VALUE ("Student Recreation Center", TRUE, dayofweek(now()), now());

select COUNT(*) from WalkerData;

