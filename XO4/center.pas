const
 x:array[1..16]of integer = (2,3,2,3,1,1,2,3,4,4,2,3,1,4,1,4);
 y:array[1..16]of integer = (2,3,3,2,2,3,1,1,2,3,4,4,1,1,4,4);
var
 k,i,j:integer;
 a:array[1..4,1..4] of integer;
begin
 read(i);

 for i:=1 to 4 do
  for j:=1 to 4 do
   read(a[i,j]);


 for k:=1 to 16 do
  if a[y[k],x[k]] = 0 then begin
   writeln(y[k], ' ', x[k]);
   halt;
  end;
end.
