var
 a:array[1..4,1..4] of integer;
 i,j:integer;
begin
 randomize;

 read(i);

 for i:=1 to 4 do
  for j:=1 to 4 do
   read(a[i,j]);

 repeat
   i:=random(4) + 1;
   j:=random(4) + 1;
 until a[i,j]=0;

 writeln(i, ' ', j);
end.
