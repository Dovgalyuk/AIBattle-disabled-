var
 k,i,j:integer;
 a:array[1..4,1..4] of integer;
begin
 read(i);

 for i:=1 to 4 do
  for j:=1 to 4 do
   read(a[i,j]);


 for k:=2 to 8 do
  for i:=1 to 4 do
   for j:=1 to 4 do
    if (k=i+j) and (a[i,j] = 0) then begin
     writeln(i, ' ', j);
     halt;
    end;
end.
