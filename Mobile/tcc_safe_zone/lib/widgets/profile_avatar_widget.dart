import 'package:flutter/material.dart';

class ProfileAvatarWidget extends StatelessWidget {
  final double radius;
  final VoidCallback? onTap;

  const ProfileAvatarWidget({
    Key? key,
    this.radius = 30,
    this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: CircleAvatar(
        radius: radius,
        backgroundColor: Colors.white,
        child: Icon(
          Icons.person,
          size: radius * 1.2,
          color: Color(0xFF007701),
        ),
      ),
    );
  }
}