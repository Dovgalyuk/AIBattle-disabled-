link_directories(${AIBATTLE_BINARY_DIR}/Execution)
add_executable(BattleShipsTester tester.cpp)
add_executable(BattleShipsSample sample.cpp)
target_link_libraries(BattleShipsTester execution)
